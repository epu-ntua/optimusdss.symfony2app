<?php

namespace Optimus\OptimusBundle\Servicios;

use Symfony\Component\Config\FileLocator;
use Doctrine\ORM\EntityManager;


class ServiceAPEconomizerPresenter {

    protected $em;
	
	private static $sensor_1_temperature_name = "Temperature (C)";	
	private static $sensor_2_humidity_name = "Humidty (%)";	
	
	// *** Enthalpy Calculation Parameters ***
	private static $c8 = -5800.2206;
	private static $c9 = 1.3914993;
	private static $c10 = -0.04860239;
	private static $c11 = 0.000041764768;
	private static $c12 = -0.000000014452093;
	private static $c13 = 6.5459673;

	private static $cpa = 1;
	private static $cpv = 1.9;
	private static $r0 = 2501;

	// *** Single Temp Calculation Parameters ***
	private static $temperature_internal = 25.00;
	private static $temperature_sup = 17.00;
	private static $temperature_lim = 12.00;

	// *** Single Enthalpy Calculation Parameters ***
	private static $enthalpy_internal = 55.50;
	private static $enthalpy_sup = 45.65;
	private static $enthalpy_lim = 40.54;
	
    
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
    public function getDataValues($idActionPlan, $idBuilding, $from, $to, $method) {
        
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
			//$temperature_external = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$rule_result = array();
			for($iHour=0; $iHour < 24; $iHour++){
				$rule_result[] = "no data";
			}
			
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
			$temperature_internal = self::$temperature_internal;
			$enthalpy_internal = self::$enthalpy_internal;
			
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($loLstDays[$iDay], $idActionPlan);
			if(!empty($qCalculation)) 
			{
				try 
				{
					$idCalculation=$qCalculation[0]->getId();
					$outputDay = $this->em->getRepository('OptimusOptimusBundle:APEconomizerOutputDay')->findOutputByDay($idCalculation, $loLstDays[$iDay]); //
					//dump($outputDay);
					if($outputDay)
					{
						$idOutputDay=$outputDay[0]->getId();		// 1, 2....
						$statusDay=$outputDay[0]->getStatus();		// 0=Unknown, 1=Accepted, 2=Declined 		
						$temperature_internal = $outputDay[0]->getTemp_internal(); // Take it from DB, outputday if the day is in the past
						$enthalpy_internal = $outputDay[0]->getEnth_internal(); // Take it from DB, outputday if the day is in the past						
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
			$temperature_external = array();
			$humidity = array();
			$enthalpy_external = array();
			if($dataHourly != null){
				$start = explode(" ", $dataHourly[0]['hour']->format("Y-m-d H:i:s"))[1]; 
				$start = explode(":", $start)[0]; 
				$start = intval($start);
				for($iHour=0; $iHour < $start; $iHour++){
					$temperature_external[$iHour] = null;
					$humidity[$iHour] = null;
				}
				
				for($iHour=$start; $iHour < $start + count($dataHourly); $iHour++){
					$temperature_external[$iHour] = $dataHourly[$iHour - $start]['temperature_external'];
					$humidity[$iHour] = $dataHourly[$iHour - $start]['humidity'] / 100;
					
					if($method == "temp"){
						if(($temperature_external[$iHour] > self::$temperature_lim) && ($temperature_external[$iHour] < self::$temperature_sup)){
							$rule_result[$iHour] = "Partial free cooling";
						}
						else{ 
							if($temperature_external[$iHour] < $temperature_internal){
								$rule_result[$iHour] = "Total free cooling";
							}
							else{
								$rule_result[$iHour] = "Minimum outside air";
							}
						}
					}
					else{
						// add enthalpy calculation
						//dump($temperature_external[$iHour]);
						$pvs = exp(	self::$c8/($temperature_external[$iHour]+273) + 
									self::$c9 + 
									self::$c10*($temperature_external[$iHour]+273) + 
									self::$c11*($temperature_external[$iHour]+273) * ($temperature_external[$iHour]+273) +
									self::$c12*($temperature_external[$iHour]+273) * ($temperature_external[$iHour]+273) * ($temperature_external[$iHour]+273) +
									self::$c13*log($temperature_external[$iHour]+273)
								  );
						//dump($pvs);
						$x = 0.622 * $humidity[$iHour] * $pvs / (101325 - $humidity[$iHour] * $pvs);
						//dump($x);
						$he = self::$cpa * $temperature_external[$iHour] + $x * (self::$cpv * $temperature_external[$iHour] + self::$r0);
						//dump($he);
						
						if(($he > self::$enthalpy_lim) && ($he < self::$enthalpy_sup)){
							$rule_result[$iHour] = "Partial free cooling";
						}
						else{ 
							if($he < $enthalpy_internal){
								$rule_result[$iHour] = "Total free cooling";
							}
							else{
								$rule_result[$iHour] = "Minimum outside air";
							}
						}
								
					}
						
				}
				
				for($iHour=$start + count($dataHourly); $iHour < 24; $iHour++){
					$temperature_external[$iHour] = null;
					$humidity[$iHour] = null;
				}
			}
			
			$aDataActionPlan[]=array("day"=>explode(" ", $loLstDays[$iDay])[0],
									 "temperature_internal"=>$temperature_internal,
									 "enthalpy_internal"=>$enthalpy_internal,
									 "rule_result" => $rule_result,
									 "method" => $method,
									 "idOutputDay"=>$idOutputDay,
									 "statusDay"=>$statusDay,
									 "nameAbbreviatedDay"=>$nameAbbreviatedDay,
									 "abbreviatedDay"=>$lsAbbreviatedDayFinal); 
			
			
		}
		
        
		
       
        return $aDataActionPlan;
    }
	
	
	
	private function getDataValuesCalculated($sCurrentDay, $idActionPlan)
	{
		$ret = array();
		
		$calculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($sCurrentDay, $idActionPlan);
		//dump($calculation);
		if($calculation != null)
		{
			if(Count($calculation)> 0)
			{
				$idCalculation=$calculation[0]->getId();
				$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $sCurrentDay);
				$output = $this->em->getRepository('OptimusOptimusBundle:APEconomizerOutput')->findOutputsByDate($idCalculation, $currentDay->format('Y-m-d H:i:s'));
				//dump($output);		
				if($output != null){
					if(Count($output) > 0){
						foreach($output as $aOutput){
							$ret[] = array( "hour"=>$aOutput->getHour(),
											"temperature_external"=>$aOutput->getTemp_external(),
											"humidity"=>$aOutput->getHumidity());
						}
						
						return $ret;
					}
				}
			}
		}   
		
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
