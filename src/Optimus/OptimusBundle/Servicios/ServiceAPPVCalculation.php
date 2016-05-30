<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;

use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APPVOutput;
use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

////////////////////////////////////////////////
// ACTION PLAN: PV BUY / SELL ENERGY PRODUCTION

class ServiceAPPVCalculation
{
    protected $em;
 
	// These names describe a unique relation between a sensor and this action plan.
    // These names can be the same to describe the same relation between a sensor and other action plans
	
 	private static $sensor_energyProduction_name = "Energy Production";	
	private static $sensor_energyConsumption_name = "Energy Consumption"; 
	private static $sensor_energyPricePurchase_name = "Energy Price Buy"; 
	private static $sensor_energyPriceSelling_name = "Energy Price Sell";
	
	private static $sensor_energyProduction_color = "#01d98e";	
	private static $sensor_energyConsumption_color = "#8900e9"; 
	private static $sensor_energyPricePurchase_color = "#9f9d9e"; 
	private static $sensor_energyPriceSelling_color = "#f39a02";	
		
	private static $sensor_energyProduction_units = "kWh";	
	private static $sensor_energyConsumption_units = "kWh"; 
	private static $sensor_energyPricePurchase_units = "€/kWh"; 
	private static $sensor_energyPriceSelling_units = "€/kWh";
	
	public function getEnergyProductionName(){return self::$sensor_energyProduction_name;}
	public function getEnergyConsumptionName(){return self::$sensor_energyConsumption_name;}
	public function getEnergyPricePurchaseName(){return self::$sensor_energyPricePurchase_name;}
	public function getEnergyPriceSellingName(){return self::$sensor_energyPriceSelling_name;}
 
    public function __construct(EntityManager $em)
    {       
		$this->em=$em;
    }
	
    public function insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding)
    {		
		// All predictions have been made previously before calling this function,
		// Now we calculate the values according to the Action Plane interafce requirements:
		
		// for each day of the week:
        for($iDay=0; $iDay<7; $iDay++)
        {
			//dump("day:".$iDay);
		
            if($iDay>0)
            {
                $actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
                if(!$actDay){
                    $actDay=\DateTime::createFromFormat('Y-m-d', $from);
                }
                $currentDay=$actDay->modify(" +".$iDay." day")->format("Y-m-d H:i:s");					
            }
            else {
                $currentDay=$from; // <--- RVIEW FORMAT!!!!!!!!!!!!!!
            }

			// ------------------------------------------------------------------------------------
            // 1. Get the "prediction" assoaciated with the current type of sensor:
            $prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($currentDay, $idBuilding);	//prediction
            //dump($prediction);

			// ------------------------------------------------------------------------------------
			// 2. Create the day values data strutcture:	
			$outvalues = array();
			$iHour = -1;
			
			// time:					
			$currentDayformated = strtotime($currentDay);
			$y=date("Y", $currentDayformated);
			$m=date("m", $currentDayformated);
			$d=date("d", $currentDayformated);
			
			// INITIATE DATA STRUCTURE:
			// Create 24 registers per hour with default values:
			while($iHour++ < 23)
			{
				$hour = date('G:i:s', mktime($iHour,0,0,$d,$m,$y));
				
				$energyProduction = 0; 						// SENSOR OK			
				$energyConsumption = 0;						//
								
				$energyPricePurchase = 1.5; 				//1.5;					FAKE! Estos datos los dará un servicio en breve (se supone que a la semana del 20-25 de Julio), por el momento se harcodean
				$energyPriceSelling = 1 + Rand(0,10)/10;	//1 + Rand(0,10)/10; 	FAKE! Estos datos los dará un servicio en breve (se supone que a la semana del 20-25 de Julio), por el momento se harcodean
								
				$outvalues[] = array("hour"=>$hour,
									 self::$sensor_energyProduction_name=>$energyProduction,
									 self::$sensor_energyConsumption_name=>$energyConsumption,
									 self::$sensor_energyPricePurchase_name=>$energyPricePurchase,
									 self::$sensor_energyPriceSelling_name=>$energyPriceSelling);
			}					
			
			$actionplan_id = $aoRelSensorsActionPlan['actionPlan']->getId();
			
			// ------------------------------------------------------------------------------------
			// 3. Get the date for each sensor:
            foreach($aoRelSensorsActionPlan['sensors'] as $sensors_partition)
            {
				foreach($sensors_partition as $apsensor) {
					
							
					
					$name=$apsensor->getName();
					$resgister_prediction=array();

					// 2. Get the "prediction" data:
					if(!empty($prediction))
					{
						$resgister_prediction=$this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate(
																															$apsensor->getFkSensor()->getId(),
																															$prediction[0]->getId(), 
																															$currentDay);
						// for the predicted data of the SENSOR for an HOUR...
						for($iData=0; $iData<count($resgister_prediction); $iData++)
						{
							$registered_prediction_sensor_hour = $resgister_prediction[$iData];
							$current_hour = $registered_prediction_sensor_hour->getDate()->format("G");
							if($current_hour!= "") {
								$outvalues[$current_hour][$name]=$registered_prediction_sensor_hour->getvalue();
								// dump($current_hour." ".$name." ".$outvalues[$current_hour][$name]);
								// dump($outvalues[$current_hour][$name]);
							}
						}											
					}
				}
			}	
			
			//dump($outvalues);
			
			// 4. Create a register for each hour in the DB:
			for($iHour=0; $iHour<count($outvalues); $iHour++)
			{
				$outvalue = $outvalues[$iHour];
				
				$datetime = date('Y-m-d H:i:s', strtotime(date("Y-m-d", $currentDayformated)." ".$outvalue["hour"]));
				
				//dump($outvalue[self::$sensor_energyProduction_name]);
				
				$output = new APPVOutput();
				$output->setFkApCalculation($calculation);	
				$output->setHour(new \DateTime($datetime));
				
				// Specific for this action plan:
				$output->setEnergyProduction($outvalue[self::$sensor_energyProduction_name] * 16.51 / 1000); //From watts to kilowatts
				$output->setEnergyConsumption($outvalue[self::$sensor_energyConsumption_name]);
				$output->setEnergyPrice($outvalue[self::$sensor_energyPricePurchase_name]/1000);	//From €/wats to €/kilowatt
				$output->setEnergyPriceSelling($outvalue[self::$sensor_energyPriceSelling_name]/1000);
								
				$this->em->persist($output);
				$this->em->flush();
			}

            // 4. Insert the "finance" strategy and "undefined" as status of the day by default. 
            $date=explode(" ", $currentDay);
            $day=\DateTime::createFromFormat('Y-m-d', $date[0]);

			$statusDay=$this->getLastCalculationDay($currentDay, $aoRelSensorsActionPlan['actionPlan']->getId());
           
			$outputDay = new APPVOutputDay();
            $outputDay->setDate($day);
			
			// Specific for this action plan:
            $outputDay->setStrategy('Finance');
            $outputDay->setStatus($statusDay);
            $outputDay->setFkApCalculation($calculation);

            $this->em->persist($outputDay);
            $this->em->flush();
        }				
    } 
	
	/* NO BORRAR (COMPROBAR ANTES QUE NO SE UTILICE)
	//Devuelve los datos para la tabla de Action plan PV
	public function getDataPVActionPlan($from, $idActionPlan)
	{
		$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		$finalDay=$actDay->modify(" +6 day")->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($from, $finalDay);
		
		$aDataActionPlan=array();
		$totalWeek=0;
		$numDays=count($aDays);
		for($i=0; $i < $numDays; $i++)
		{
			$aDataCalculation=array();
			$consume=$difference=$averageDay=$production=$purchase=$selling=$dailyAcumulated=0;
			$idStatusDay=0;
			$idCalculation=0;
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			
			if(!empty($qCalculation)) //hay calculo para el dia
			{
				$idCalculation=$qCalculation[0]->getId();
													
				//get outputs
				$aRegisterCalculation = $this->em->getRepository('OptimusOptimusBundle:APPVOutput')->findResgisterOutputsByDate($idCalculation, $aDays[$i]);
					
				$numRegisters=0;				
				foreach($aRegisterCalculation as $register)
				{
					$aDataCalculation[]=array("energy_price"=>$register->getEnergyPrice(), "energy_production"=>$register->getEnergyProduction(), "date"=>$register->getHour()->format("Y-m-d H:i:s"));
					
					$averageDay=$averageDay+$register->getEnergyProduction();
					$production=$production+$register->getEnergyProduction();
					$consume=$consume+($register->getEnergyProduction()*2/200); 
					$difference=$difference+($register->getEnergyProduction()-($register->getEnergyProduction()*2/200));
					$purchase=$register->getEnergyPrice();
					$selling=$register->getEnergyPrice();
					$dailyAcumulated=$dailyAcumulated + $register->getEnergyProduction();
					
					$numRegisters++;	
				}
				if($averageDay>0) $averageDay=$averageDay/$numRegisters;
			}
			
			$dayLookFor=\DateTime::createFromFormat("Y-m-d H:i:s", $aDays[$i]);
			$dayFinal=explode(" ", $aDays[$i]);
			
			//dump($dayFinal);
			//add idOutputDay
			$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $dayFinal[0]);
			//dump($outputDay);
			if($outputDay)
			{
				$idOutputDay=$outputDay[0]->getId();
				$status=$outputDay[0]->getStatus();
				$strategy=$outputDay[0]->getStrategy();
			}else{
				$idOutputDay=0;
				$status=0;
				$strategy="";
			}
			
			$aDataActionPlan[]=array(	"day"=>$aDays[$i], 
										"calculation"=>$aDataCalculation, 
										"averageDay"=>$averageDay, 
										"production"=>$production, 
										"consume"=>$consume, 
										"difference"=>$difference, 
										"purchase"=>$purchase, 
										"selling"=>$selling, 
										"dailyAcumulated"=>$dailyAcumulated, 
										"idCalculation"=>$idCalculation, 
										"idOutputDay"=>$idOutputDay, 
										"status"=>$status, 
										"strategy"=>$strategy,
										"idActionPlan"=>$idActionPlan); //change idStatus
		}
		//dump($aDataActionPlan);
		
		return $aDataActionPlan;
	}
	
	
	//Devuelve un array con todos los días entre 2 fechas
	private function getDaysFromDate($from, $to)
	{
		$aDays=array();
		$aDays[0]=$from;
		for($i=1; $i<7; $i++)
		{
			$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
			$act=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
			if(($act) < $to)
				$aDays[$i]=$act;
			else break;
		}		
		$aDays[$i]=$to;		
		
		return $aDays;
	}	
	*/

	private function getLastCalculationDay($day, $idActionPlan)
	{
		$status=0;
		
		$qLastCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($day, $idActionPlan);
		if($qLastCalculation)
		{
			if(isset($qLastCalculation[1]))
			{
				$idCalculation=$qLastCalculation[1]->getId();
				
				//Buscamos el status
				$dayFinal=explode(" ", $day)[0];
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $dayFinal); //
				if($outputDay)
				{				
					$status=$outputDay[0]->getStatus();						
				}
			}
		}
		return $status;
	}
	
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[].=self::$sensor_energyProduction_name;
		$aVariablesInput[].=self::$sensor_energyConsumption_name;
		$aVariablesInput[].=self::$sensor_energyPricePurchase_name;
		$aVariablesInput[].=self::$sensor_energyPriceSelling_name;
		
		return $aVariablesInput;
	}
	
	public function getColorsVariables()
	{
		$data = array();
		$data[self::$sensor_energyProduction_name] = self::$sensor_energyProduction_color;
		$data[self::$sensor_energyConsumption_name] = self::$sensor_energyConsumption_color;
		$data[self::$sensor_energyPricePurchase_name] = self::$sensor_energyPricePurchase_color;
		$data[self::$sensor_energyPriceSelling_name] = self::$sensor_energyPriceSelling_color;
		
		return $data;
	}
	
	public function getUnitsVariables()
	{
		$data = array();
		$data[self::$sensor_energyProduction_name] = self::$sensor_energyProduction_units;
		$data[self::$sensor_energyConsumption_name] = self::$sensor_energyConsumption_units;
		$data[self::$sensor_energyPricePurchase_name] = self::$sensor_energyPricePurchase_units;
		$data[self::$sensor_energyPriceSelling_name] = self::$sensor_energyPriceSelling_units;
		
		return $data;
	}
}
?>