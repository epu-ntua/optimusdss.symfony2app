<?php

namespace Optimus\OptimusBundle\Servicios;

use Symfony\Component\Config\FileLocator;
use Doctrine\ORM\EntityManager;



class ServiceAPEnergySourcePresenter {

    protected $em;
    
	// Sensors
	//protected $ontologia;
	//protected $invokePredictData; 
	protected $events;
	private static $sensor_PV_rad = "PV_rad";	
	private static $sensor_PV_Pel = "PV_Pel"; 
	private static $sensor_CHP_C65B_Pel = "CHP_C65B_Pel"; 
	private static $sensor_CHP_C65_Pel = "CHP_C65_Pel";
	private static $sensor_STO_Pel = "STO_Pe";
	private static $sensor_NET_Pel = "NET_Pel";		
	private static $sensor_BO_Pth = "BO_Pth";	
	
	public function getPV_radName(){return self::$sensor_PV_rad;}
	public function getPV_PelName(){return self::$sensor_PV_Pel;}
	public function getCHP_C65B_PelName(){return self::$sensor_CHP_C65B_Pel;}
	public function getCHP_C65_PelName(){return self::$sensor_CHP_C65_Pel;}
	public function getCHP_STO_PelName(){return self::$sensor_STO_Pel;}
	public function getNET_PelName(){return self::$sensor_NET_Pel;}
	public function getCHP_BO_PthName(){return self::$sensor_BO_Pth;}
	
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[].=self::$sensor_PV_rad;
		$aVariablesInput[].=self::$sensor_PV_Pel;
		$aVariablesInput[].=self::$sensor_CHP_C65B_Pel;
		$aVariablesInput[].=self::$sensor_CHP_C65_Pel;
		$aVariablesInput[].=self::$sensor_STO_Pel;
		$aVariablesInput[].=self::$sensor_NET_Pel;
		$aVariablesInput[].=self::$sensor_BO_Pth;
		
		return $aVariablesInput;
	}
	
	//Sensors\
	
	
    public function __construct(EntityManager $em)
    {
        $this->em=$em;
    }
	
    /*
     * Run the service
     * The result is an array containing
     *   - Input from sensors (e.g 'actual_humidity', 'air_temperature')
     *   - Feedback from TCV ('feedback')
     *   - The proposed temperature ('proposed_temperature')
     */
    public function getDataValues($idActionPlan, $idBuilding, $from, $to) {
        
		$from = $this->getDateString($from, 0)." 00:00:00"; // We ensure that we have datatime
		if($to == null){
			$to=\DateTime::createFromFormat('Y-m-d H:i:s', $from)->modify(" +6 day")->format("Y-m-d H:i:s");
		}else{
			$to = $this->getDateString($to, 0)." 00:00:00"; // We ensure that we have datatime
		}
		
		$loLstDays=$this->getDaysFromDate($from, $to);	// Returns an array of every day between two dates	
		$nDays=count($loLstDays);						// Number of days: should be 7 (days)	
		
		
		$sensors = array();
		$actionPlanSensors = $this->em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_actionplan"=>$idActionPlan));
		foreach($actionPlanSensors as $actionPlanSensor) {
			$sensors[] = array("sensor"=>$actionPlanSensor->getFkSensor(),
							   "name"=>$actionPlanSensor->getName());
		}	
		
		//dump($sensors);
		
		$aDataActionPlan=array();
		
		// for each day from starting date to 6 more days ...
		for($iDay=0; $iDay < $nDays; $iDay++)
		{
			$load = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$load_total = 0;
			$grid = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$grid_total = 0;
			$res = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$res_total = 0;
			$shaving = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$load_original = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$load_original_total = 0;
			$grid_original = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$grid_original_total = 0;
			$storage = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			
			
				
			if($iDay>0)
            {
                $currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
                if(!$currentDay){
                    $currentDay=\DateTime::createFromFormat('Y-m-d', $from);
                }
                $currentDay=$currentDay->modify(" +".$iDay." day")->format("Y-m-d H:i:s");					
            }
            else {
                $currentDay=$from;
            }
		
			$lsAbbreviatedDay=\DateTime::createFromFormat("Y-m-d H:i:s", $loLstDays[$iDay]);
			$lsAbbreviatedDayFinal = $lsAbbreviatedDay;
			$lsAbbreviatedDayFinal = $lsAbbreviatedDayFinal->format('d-m');
			$nameAbbreviatedDay = $lsAbbreviatedDay->format('D');
			
			$idCalculation=0;
			$idOutputDay=0;
			$statusDay=0;
			
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($loLstDays[$iDay], $idActionPlan);
			if(!empty($qCalculation)) 
			{
				try 
				{
					$idCalculation=$qCalculation[0]->getId();
					$outputDay = $this->em->getRepository('OptimusOptimusBundle:APFlowsOutputDay')->findOutputByDay($idCalculation, $loLstDays[$iDay]); //
					//dump($outputDay);
					if($outputDay)
					{
						$idOutputDay=$outputDay[0]->getId();		// 1, 2....
						$statusDay=$outputDay[0]->getStatus();		// 0=Unknown, 1=Accepted, 2=Declined 	
					}
				} 
				catch (Exception $e) 
				{
					//echo 'Excepción capturada: ',  $e->getMessage(), "\n";
					continue;
				}
			}
			
			$dataHourly = $this->getDataValuesCalculated($currentDay, $idActionPlan);
			//dump($dataHourly);
			//print_r($dataHourly);
			if($dataHourly != null){
				for($iHour=0; $iHour < 24; $iHour++){
					$load[$iHour] = $dataHourly[$iHour]['load'];
					$grid[$iHour] = $dataHourly[$iHour]['grid'];
					$res[$iHour] = $dataHourly[$iHour]['res'];
					$shaving[$iHour] = $dataHourly[$iHour]['shaving'];
					$load_original[$iHour] = $dataHourly[$iHour]['load_original'];
					$grid_original[$iHour] = $dataHourly[$iHour]['grid_original'];
					$storage[$iHour] = $dataHourly[$iHour]['storage'];
					
					$load_total += $dataHourly[$iHour]['load'];
					$load_original_total += $dataHourly[$iHour]['load_original'];
					$grid_total += $dataHourly[$iHour]['grid'];
					$grid_original_total += $dataHourly[$iHour]['grid_original'];
					$res_total += $dataHourly[$iHour]['res'];
				}
			}
			
			
			$aDataActionPlan[]=array("day"=>explode(" ", $loLstDays[$iDay])[0], 
									 "load"=>$load,
									 "grid"=>$grid,
									 "res"=>$res,
									 "shaving"=>$shaving,
									 "load_original"=>$load_original,
									 "grid_original"=>$grid_original,
									 "storage"=>$storage,
									 "load_total"=>$load_total, 							// Totals	
									 "grid_total"=>$grid_total, 							// Totals
									 "res_total"=>$res_total, 								// Totals
									 "load_original_total"=>$load_original_total, 			// Totals	
									 "grid_original_total"=>$grid_original_total, 			// Totals
									 "idOutputDay"=>$idOutputDay,
									 "statusDay"=>$statusDay,
									 "nameAbbreviatedDay"=>$nameAbbreviatedDay,
									 "abbreviatedDay"=>$lsAbbreviatedDayFinal); 
			
			//for($i=0; $i < 24; $i++){
			//	for($j=0; $j < 7; $j++){
			//		print_r( $aDataActionPlan[$j]);
			//		}
			//		}
			
		}
		
		
       
        return $aDataActionPlan;
    }
	
	
	
	private function getDataValuesCalculated($sCurrentDay, $idActionPlan)
	{
		$ret = array();
		
		$calculations = $this->em->getRepository('OptimusOptimusBundle:APCalculation')->findAllCalculationsByDate($sCurrentDay, $idActionPlan);
		if($calculations != null){
			if(Count($calculations)> 0){
				foreach ($calculations as $calculation) {
					$idCalculation=$calculation->getId();
					$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $sCurrentDay);
					$output = $this->em->getRepository('OptimusOptimusBundle:APFlowsOutput')->findFlowsOutputsByDate($idCalculation, $currentDay->format('Y-m-d H:i:s'));
					if($output != null){
						if(Count($output) > 0){
							foreach($output as $aOutput){
								$ret[] = array( "hour"=>$aOutput->getHour_timestamp(),
												"load"=>$aOutput->getLoad_value(),
												"grid"=>$aOutput->getGrid(),	
												"res"=>$aOutput->getRes(),							
												"shaving"=>$aOutput->getShaving(),							
												"load_original"=>$aOutput->getLoad_original(),							
												"grid_original"=>$aOutput->getGrid_original(),							
												"storage"=>$aOutput->getStorage() );
							}
							return $ret;
						}
					}
				}
			}
		}
		
		
	/*	
		$calculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($sCurrentDay, $idActionPlan);
		//dump($calculation);
		if($calculation != null)
		{
			if(Count($calculation)> 0)
			{
				$idCalculation=$calculation[0]->getId();
				$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $sCurrentDay);
				$output = $this->em->getRepository('OptimusOptimusBundle:APFlowsOutput')->findFlowsOutputsByDate($idCalculation, $currentDay->format('Y-m-d H:i:s'));
				//dump($output);
				//print_r($output);				
				if($output != null){
					if(Count($output) > 0){
						foreach($output as $aOutput){
							$ret[] = array( "hour"=>$aOutput->getHour_timestamp(),
											"load"=>$aOutput->getLoad_value(),
											"grid"=>$aOutput->getGrid(),	
											"res"=>$aOutput->getRes(),							
											"shaving"=>$aOutput->getShaving(),							
											"load_original"=>$aOutput->getLoad_original(),							
											"grid_original"=>$aOutput->getGrid_original(),							
											"storage"=>$aOutput->getStorage() );
						}
						return $ret;
					}
				}
			}
		}
	*/	
		return null; // Not defined
	}
	
	
	
	private function getDateString($from, $iDay)
	{
		$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		if(!$currentDay){
			$currentDay=\DateTime::createFromFormat('Y-m-d', $from);
		}
		return $currentDay->modify(" +".$iDay." day")->format("Y-m-d");
	}
	
	
	// Returns an array of every day between two dates:
	private function getDaysFromDate($from, $to)
	{
		$aDays=array();
		$aDays[0]=$from;
		for($i=1; $i<7; $i++)
		{
			$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
			$act=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
			if(($act) < $to){
				$aDays[$i]=$act;
			} else {
				break;
			}
		}		
		$aDays[$i]=$to;		
		
		return $aDays;
	}
	
}
?>
