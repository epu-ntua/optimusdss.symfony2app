<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;
use Optimus\OptimusBundle\Servicios\ServiceCalculationDataInvoke;
use Optimus\OptimusBundle\Servicios\ServicePredictDataInvoke;
use Optimus\OptimusBundle\Servicios\ServiceAPPV;
use Optimus\OptimusBundle\Servicios\ServiceAPPVM;
use Optimus\OptimusBundle\Servicios\ServiceAPEconomizer;
use Optimus\OptimusBundle\Servicios\ServiceAPTSV;
use Optimus\OptimusBundle\Servicios\ServiceAPPreheating;
use Optimus\OptimusBundle\Servicios\ServiceAPAdaptative;
use Optimus\OptimusBundle\Servicios\ServiceEvents;
use Optimus\OptimusBundle\Servicios\ServicePricePredictor;

use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class ServiceCalculos {
	
	protected $ontologia;	
	protected $invokeCalculationData; 
	protected $invokePredictData; 
    protected $em;
    protected $appv;
	protected $appvm;
	protected $apeconomizer;
    protected $appreheating;
    protected $apspmc;
	protected $apoc;
    protected $aptsv;
    protected $event;
	protected $pvm_num_panels;
	protected $pvm_panels_surface_area;
	protected $pvm_a_coefficient;
	protected $pvm_ta_coefficient;
	protected $pricePredictor;
 
    public function __construct(ServiceOntologia $ontologia,
								ServiceCalculationDataInvoke $invokeCalculationData,
								ServicePredictDataInvoke $invokePredictData,
								EntityManager $em,
								ServiceAPPVCalculation $appv,
								ServiceAPPVMCalculation $appvm,
								ServiceAPEconomizerCalculation $apeconomizer,
								ServiceAPPreheating $appreheating,
								ServiceAPSPMCalculation $apspmc,	
								ServiceAPOCalculation $apoc,	
								ServiceEvents $event,
								ServicePricePredictor $pricePredictor,
								$pvm_num_panels,
								$pvm_panels_surface_area,
								$pvm_a_coefficient,
								$pvm_ta_coefficient)
    {
		// Params: htdocs\optimus\app\config\config.yml
		//         htdocs\optimus\src\Optimus\OptimusBundle\Resources\config\services.yml
		
        $this->ontologia=$ontologia;
        $this->invokeCalculationData=$invokeCalculationData;
		$this->invokePredictData=$invokePredictData;
		$this->em=$em;
		$this->appv=$appv;
		$this->appvm=$appvm;
		$this->apeconomizer=$apeconomizer;
		$this->appreheating=$appreheating;
		$this->apspmc=$apspmc;
		$this->apoc=$apoc;
		$this->event=$event;		
		$this->pricePredictor=$pricePredictor;		
		$this->pvm_num_panels=$pvm_num_panels;
		$this->pvm_panels_surface_area=$pvm_panels_surface_area;
		$this->pvm_a_coefficient=$pvm_a_coefficient;
		$this->pvm_ta_coefficient=$pvm_ta_coefficient;
    }
	
	public function createPredictionAndCalculatesAllBuildings($ip, $user)
	{
		$dateActual=new \DateTime();
		
		$buildings=$this->em->getRepository('OptimusOptimusBundle:Building')->findAll();

		foreach($buildings AS $building) {
			$this->createPredictionAndCalculates($dateActual->format("Y-m-d"), $building->getId(), $ip, $user);
		}
	}

		
	public function createPredictionAndCalculates($from='', $idBuilding, $ip, $user)
	{
		// // TEST:	
		//$aActionsPlans=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("status"=>1, "fk_Building"=>$idBuilding));
		//$aoRelSensorsActionPlans=$this->getSensorsByActionPlan($aActionsPlans, $idBuilding, $ip, $user);
		//$calculation = $this->em->getRepository('OptimusOptimusBundle:APCalculation')->find(268);
		//$aoRelSensorsActionPlans[0]['sensors'] = array(1,2);
		//dump($aoRelSensorsActionPlans[0]);
		//$this->appvm->insertPVMOutput($aoRelSensorsActionPlans[0], $from, $calculation, $idBuilding);		
		//$this->appvm->insertNewCalculation(array("actionPlan"=>$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->find(19)), $from, $idBuilding);		
		//return;		
	
		// 1. PREDICTION (independent from the Action Plan)
		$this->newPrediction($from, $idBuilding, $ip, $user);		// Predict for all services 
		
		// 2. CALCULATION (it depends on the action plan)
		$this->createCalculations($from, $idBuilding, $ip, $user);
	}
	
	// 1. PREDICTION ------------------------------------------------------------------------------
	
	private function newPrediction($from='', $idBuilding, $ip, $user)
	{
		// Remember: for PREDICTIONS, all services need to be invoked only one time at night!				
		// (such services should never be invoked by user demands when they interact with AP interfaces).
	
		$idPrediction=$this->createPrediction($from, $idBuilding, $ip, $user);
		$aSensorValues=$this->loadXml($from, $idBuilding);
		if($aSensorValues!= null){
			$this->insertPredictions($aSensorValues, $idPrediction);		
		}
	}
	
	private function createPrediction($from='', $idBuilding, $ip, $user)
	{
		$from=$from." 00:00:00";
		$dateCreate=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		$dateUser=new \DateTime();
				
		$namePrediction="Prediction_".$dateCreate->format('Y-m-d H:i:s')."_".$dateUser->format('Y-m-d H:i:s');
		
		$building=$this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		
		$prediction = new Prediction();
		$prediction->setName($namePrediction);
		$prediction->setDateCreate($dateCreate);
		$prediction->setDateUser($dateUser);
		$prediction->setFkBuilding($building);
	 	
		$this->em->persist($prediction);
		$this->em->flush();	
		
		//dump("New prediction: ".$prediction->getId());
		
		//create event new prediction
		$this->event->createEvent(NULL , "has carried out a new prediction for the next 7 days", "OPTIMUS", NULL , 1, $ip, $idBuilding, "create");
			
		return $prediction->getId();		
	}
	
	private function loadXml($from, $idBuilding)
	{			
		$startDate=$from."T00:00:00Z";
		$window=169;
		$horizon=169;
		$aSensors=array();
		
		$sensorsBuilding=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array("fk_Building"=>$idBuilding));
		
		if($sensorsBuilding)
		{
			foreach($sensorsBuilding as $sensor)
			{
				set_time_limit(0);
				
				//dump($sensor);

				$urlService = $sensor->getUrlService();
				$predictionmodelparameters = $sensor->getPredictionmodelparameters();
				
				if($urlService == 'pricepredictor') {
					$to=$from." 00:00:00";
					$dInit=\DateTime::createFromFormat('Y-m-d H:i:s', $to)->modify('-1 month')->format('Y-m-d H:i:s');
					
					$aPricePredictor=$this->pricePredictor->getPricePredictor($dInit, $to,  $sensor->getName(), 'variable', $idBuilding); //'2015-09-13 00:00:00',  '2015-10-13 00:00:00'
					
  					echo "Predict data for: ".$sensor->getName()." | ".$sensor->getId()." (price)\n";

					//dump($aPricePredictor);
					
					$aValues=array();
					$i=0;
					foreach($aPricePredictor as $dayPrediction) 
					{
						$day=\DateTime::createFromFormat('Y-m-d H:i:s', $to)->modify('+'.$i.' day');
						
						$j=0;
						foreach($dayPrediction as $hourPrediction)
						{
							$timeActual=\DateTime::createFromFormat('Y-m-d H:i:s', $day->format('Y-m-d H:i:s'))->modify('+'.$j.' hour');
							$aValues[]=array("value"=>(int)$hourPrediction, "dateElement"=>$timeActual);
							 $j++;
						}
						$i++;						
					}
					
					$aSensors[]=array("sensor"=>$sensor, "values"=>$aValues);
					
					//dump("------ Predictor --------");
					//dump($aSensors);
					//dump("-------------------------");
					
				}
				else if($urlService != '' && $predictionmodelparameters != '' && $predictionmodelparameters != '-')
				{
					echo "Predict data for: ".$sensor->getName()." | ".$sensor->getId()."\n";

					//If sensor is "Energy price" call to service "PricePredictor"------------------------------------------
					// Get the prediction data for each Sensor (Energy production, Solar radiation, ...)
					$prevXml=$this->invokePredictData->PredictData(	$urlService, 														 
																	$startDate,
																	$window, 
																	$horizon,
																	$predictionmodelparameters);
					
					$xml = simplexml_load_string($prevXml);
					if($xml!==false)
					{
						$i=0;
						$aValues=array();
						foreach ($xml as $example) 
						{
							if($i<1)
							{	
								$j=0;
								foreach($example as $key=>$element)
								{						
									$nameKey=key($element);
									
									$date=\DateTime::createFromFormat('Y-m-d H:i:s', $from.' 00:00:00');
									$actDay=$date->modify("+".$j." hour");					
									
									/*$date=(string)$element->datetime;
									$date1=str_replace("T", " ", $date);
									$date2=substr($date1, 0, -1);					
									$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $date2);*/					
									
									//$aValues[]=array("value"=>(int)$element->$nameKey, "dateElement"=>$dateElement);
									$aValues[]=array("value"=>(int)$element->prediction, "dateElement"=>$actDay);
									
									$j++;
								}
							}			
							$i++;
						}
						
						if($aValues == null) 
						{
							//dump("Error: no values are specified for the sensor '".$sensor->getName()."'");
							return null;
						}
						
                        echo "Dara retrieved: ".count($aValues)."\n\n";
                        
						$aSensors[]=array("sensor"=>$sensor, "values"=>$aValues);
					}
					else
					{
						// TODO -> TIMEOUT is not controled to wait until new call???
						//dump("Error: calling to web service. Probably, timeout is not enough");
					}
				}
			}
		}
		
		// dump("SENSORS: ".$aSensors);		
		return $aSensors;
	}
	
	private function insertPredictions($aSensors, $idPrediction)
	{		
		$prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->find($idPrediction);		
		
		foreach($aSensors as $sensor)
		{
			if($sensor['values'] == null) {
				//dump("Error: no values are specified for the sensor '".$sensor['sensor']->getName()."'");
			}
			else
			{
				// All prediction values in -> table: RegisterPredictions
			
				foreach($sensor['values'] as $element)
				{					
					$register = new RegisterPredictions();
					$register->setDate($element['dateElement']);
					$register->setValue($element['value']);
					$register->setFkSensor($sensor['sensor']);
					$register->setFkPrediction($prediction);
					
					$this->em->persist($register);
					$this->em->flush();
				}
			}
		}
	}
	
	// 2. CALCULATION -----------------------------------------------------------------------------
  
	public function createCalculations($from='', $idBuilding, $ip, $user)
	{
		// 1. Search for active Action Plans (status=1) for the current building:
		$aActionsPlans=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("status"=>1, "fk_Building"=>$idBuilding));
		
		// 2. Retrieve all data (from each sensor type) filtered by active 'Action Plans':
		$aoRelSensorsActionPlans=$this->getSensorsByActionPlan($aActionsPlans, $idBuilding, $ip, $user);
		
		// 3. Register new calculation datasets generated for each Action Plan:
		$aoRelCalculationsActionPlans=$this->createNewCalculations($aoRelSensorsActionPlans, $from);
		
		// 4. Insert the new calculated data into the database (tables):
		$this->insertOutputsActionPlan($aoRelSensorsActionPlans, $from, $aoRelCalculationsActionPlans, $idBuilding, $ip, $user);
	}	
	
	private function getSensorsByActionPlan($aoActionsPlans, $idBuilding, $ip, $user)
	{
		$aoRelSensorsActionPlans=array();
		
		// For each Action Plan...
		foreach($aoActionsPlans as $oActionPlan)
		{			
			$aoSensors=array();			
			
			// Given an "action plan"(1), find "rel_ap_sensors"(N) associated:
			$aoRel_AP_Sensors=$this->em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_actionplan"=>$oActionPlan->getId()), array("orderSensor"=>"ASC"));
			//$aoRel_AP_Sensors=$this->em->getRepository('OptimusOptimusBundle:APSensors')->getAPSensorsOrder($oActionPlan->getId());
				
			//dump($aoRel_AP_Sensors);  // REL: fk_actionplan, fk_sensor, fk_BuildingPartitioning (e.g. {10,1,46} {10,1,47} {10,1,48})
				
			// For each relation "rel_ap_sensors"...
			foreach($aoRel_AP_Sensors as $aoRel_AP_Sensor)
			{
				// Get sensors for the current action plan:
				$oSensor=$this->em->getRepository('OptimusOptimusBundle:Sensor')->find($aoRel_AP_Sensor->getFkSensor());
				
				$aoSensors[$aoRel_AP_Sensor->getFkBuildingPartitioning()->getId()][] = $aoRel_AP_Sensor;
			}
			
			$aoRelSensorsActionPlans[]=array("actionPlan"=>$oActionPlan, "sensors"=>$aoSensors); // ex:  "sensors" = [0:1, ...]
			
			//create events action plans
			//$this->event->createEvent($user, "new calculation ", "Action Plan -".$oActionPlan->getName()."", $oActionPlan->getId(), 1, $ip, $idBuilding, "new calculation");
		}		
		
		//dump($aoRelSensorsActionPlans);
		return $aoRelSensorsActionPlans;		
	}

	private function createNewCalculations($aoSensorsActionPlan, $from)
	{
		// Register a new calculation:
	
		$from=$from." 00:00:00";
		$dateCreate=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		//$dateCreate=new \DateTime(); //$from
		$aoNewCalculation = array();
		
        $today = new \DateTime();
        $today = $today->format("Y-m-d");
        
        //var_dump($dateCreate);
		foreach($aoSensorsActionPlan as $aSensorsActionPlan)
		{
			//dump($actionPlan);
			$dateUser=new \DateTime();
			$actionPlan=$aSensorsActionPlan['actionPlan'];
			
            
			$newCalculation=new APCalculation();
			$newCalculation->setFkActionplan($actionPlan);
			
            if($today == $from) {
                //If from is equal to today, then for this action plan we have to move back 8 days
                switch($actionPlan->getType()){
                    case 5:
                        $newfrom = \DateTime::createFromFormat('Y-m-d' , date('Y-m-d', strtotime($from. ' - 7 days')));
                        $newCalculation->setStartingDate($newfrom);
                        break;
                    default:
                        $newCalculation->setStartingDate($dateCreate);
                }
            } else {
                $newCalculation->setStartingDate($dateCreate);
            }
            
            $newCalculation->setDateCreation($dateUser);	 
			
			$this->em->persist($newCalculation);
			$this->em->flush();
			
			//dump("New APCalculation: ".$newCalculation->getId()." for the AP -> ".$newCalculation->getFkActionplan()->getId());
			
			$aoNewCalculation[]=array(	'calculation'=>$newCalculation->getId(),
										'actionPlan'=>$actionPlan);
		}
		
		return $aoNewCalculation;
	}

	private function insertOutputsActionPlan($aoRelSensorsActionPlans, $from, $aoRelCalculationsActionPlans, $idBuilding, $ip, $user)
	{
		//dump($aoRelSensorsActionPlans);
		
		// Info: there is only one calculation for each actionPlan
		
		$from=$from." 00:00:00";		
				
		foreach($aoRelSensorsActionPlans as $aoRelSensorsActionPlan)
		{	
			$idActionPlan = $aoRelSensorsActionPlan['actionPlan']->getId();
			if($idActionPlan!=null)
			{
				foreach($aoRelCalculationsActionPlans as $aoRelCalculationActionPlan)
				{
					if (array_key_exists('actionPlan', $aoRelCalculationActionPlan)) 
					{
						if($aoRelCalculationActionPlan['actionPlan']->getId() == $idActionPlan)
						{
							$idCalculation = $aoRelCalculationActionPlan['calculation'];
							$calculation = $this->em->getRepository('OptimusOptimusBundle:APCalculation')->find($idCalculation);
							
							if(	$calculation!=null &&
								$aoRelSensorsActionPlan!= null)
							{
								//dump(	"INSERT NEW CALCULATED DATA: ".$aoRelSensorsActionPlan['actionPlan']->getType()." Calc ->".$calculation->getId()." AP -> ".$idActionPlan);

								// Do calculations...
								switch($aoRelSensorsActionPlan['actionPlan']->getType())
								{
   									case 1: $this->apoc->insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding, $ip, $user); break;
									case 2: $this->apspmc->insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding, $ip, $user); break;
									// ON/OFF heating system
                                    case 4: $this->appreheating->insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding, $ip, $user); break; //Ip & User for event of alarms
									// Scheduling PV maintenance
                                    case 5: $this->appvm->insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding, $ip, $user); break; //Ip & User for event of alarms
									// Scheduling the buy/sell energy produced PV
                                    case 6: $this->appv->insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding); break;
									// Scheduling the air side economizer
                                    case 8: $this->apeconomizer->insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding, $ip, $user); break;

                                }
							}
						}
					}
				}							
			}			
		}
	}		
}

?>