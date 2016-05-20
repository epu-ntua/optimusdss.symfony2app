<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Optimus\OptimusBundle\Servicios\GestorOntologia;

class ServiceAPPVMPresenter
{
	// Variables members --------------------------------------------------------------------------
	
    protected $em;
	protected $ontologia;
	
	private static $sensor_energyProduction_name = "Energy Production";		
	private static $sensor_solarRadiation_name = "Solar Radiation";			
	
    //private static $sensor_1_temperature_name = "Temperature (C)";	
	//private static $sensor_2_humidity_name = "Humidty (%)";	
	//private static $sensor_3_windspeed_name = "Wind speed( KMh)";	
    //private static $sensor_4_winddirection_name = "Wind direction (Degrees)";	
    //private static $sensor_5_pressure_name = "Pressure (Pa)";	
	private static $sensor_6_solarradiation_name = "solar radiation (Watts/m2)";	
	//private static $sensor_7_cloudcover_name = "Cloud cover (%)";	
    //private static $sensor_8_rainfall_name = "Rain fall (mm)";	
    //private static $sensor_9_snowfall_name = "Snow fall (cm)";	
    //private static $sensor_10_weathercondition_name = "Weather condition (W/m2)";	
    //private static $sensor_11_confidencelevel_name = "Confidence level (%)";	
    private static $sensor_12_energyproduction_name = "Energy production (Kwh)";	
    
	// Constructors -------------------------------------------------------------------------------
	
	public function __construct(EntityManager $em,
								ServiceOntologia $ontologia)
    {
		// Params: htdocs\optimus\app\config\config.yml
		//         htdocs\optimus\src\Optimus\OptimusBundle\Resources\config\services.yml
	
        $this->ontologia=$ontologia;
		$this->em=$em;		
    }
	
	// Methods ------------------------------------------------------------------------------------
	
	public function getDataValues($idActionPlan, $idBuilding, $from, $to)
	{
		$from = $this->getDateString($from, 0)." 00:00:00"; // We ensure that we have datatime
		if($to == null){
			$to=\DateTime::createFromFormat('Y-m-d H:i:s', $from)->modify(" +6 day")->format("Y-m-d H:i:s");
		}else{
			$to = $this->getDateString($to, 0)." 00:00:00"; // We ensure that we have datatime
		}
		
		$loLstDays=$this->getDaysFromDate($from, $to);	// Returns an array of every day between two dates	
		$nDays=count($loLstDays);						// Number of days: should be 7 (days)	
		
		// Current day is used to know if we are dealing with historical data or predicted:
		$today = new \DateTime();
		$today = $today->format('Y-m-d H:i:s');
		
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
			$production_historical = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$production_predicted = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$solarRadiation_historical = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");	
			$solarRadiation_predicted = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			$alarmPower = array("0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0");
			
			$production_historical_total = 0;
			$production_predicted_total = 0;
			$solarRadiation_historical_total = 0;	
			$solarRadiation_predicted_total = 0;
			
			$status = 0; // 0 = OK, 1 = ERROR TEMP, 2 = ERROR POWER		
		
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
			$dayAlert = 0;
			
			// This is a future day?
			$future_day = false;
			if($currentDay >= $today){
				$future_day = true;	
			}
			
			
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($loLstDays[$iDay], $idActionPlan);
			if(!empty($qCalculation)) 
			{
				try 
				{
					$idCalculation=$qCalculation[0]->getId();
					//dump($idCalculation.$loLstDays[$iDay]);
					$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVMOutputDay')->findOutputByDay($idCalculation, $loLstDays[$iDay]); //
					if($outputDay)
					{
						$idOutputDay=$outputDay[0]->getId();		// 1, 2....
						$statusDay=$outputDay[0]->getStatus();		// 0=Unknown, 1=Accepted, 2=Declined 		
						$dayAlert = $outputDay[0]->getAlert();		// Predicted Alarm Status				
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
			//$dayAlert = 0;
			if($dataHourly != null){
				for($iHour=0; $iHour < count($dataHourly); $iHour++){
					$production_historical[$iHour] = $dataHourly[$iHour]['pvProduced'];
					$production_predicted[$iHour] = $dataHourly[$iHour]['pvPredicted'];
					$alarmPower[$iHour] = $dataHourly[$iHour]['alarmPower'];
					
					$production_historical_total += $dataHourly[$iHour]['pvProduced'];
					$production_predicted_total += $dataHourly[$iHour]['pvPredicted'];
				}
				$dayAlert = $dataHourly[0]['dayAlert'];
			}
			
			
			// 4. GET ADDITIONAL INFORMATION:			
			$production_difference=$this->getDifferenceDay($production_historical, $production_predicted, $future_day);
			//dump($production_historical);
			$aDataActionPlan[]=array("day"=>explode(" ", $loLstDays[$iDay])[0], 
									 "production_historical"=>$production_historical,
									 "production_predicted"=>$production_predicted,
									 "solarRadiation_historical"=>$solarRadiation_historical,
									 "solarRadiation_predicted"=>$solarRadiation_predicted,
									 "production_difference"=>$production_difference,
									 "alarmPower"=>$alarmPower,
									 "production_historical_total"=>$production_historical_total, 			// Totals	
									 "production_predicted_total"=>$production_predicted_total, 			// Totals
									 "solarRadiation_historical_total"=>$solarRadiation_historical_total, 	// Totals	
									 "solarRadiation_predicted_total"=>$solarRadiation_predicted_total,		// Totals									 									 
									 "alert"=>$dayAlert,												// 0=OK, 1=POWER, 2=TEMP (not used) 3=FUTURE
									 "idOutputDay"=>$idOutputDay,
									 "statusDay"=>$statusDay,
									 "status"=>$status,														// Not used
									 "future_day"=>$future_day,
									 "nameAbbreviatedDay"=>$nameAbbreviatedDay,
									 "abbreviatedDay"=>$lsAbbreviatedDayFinal); 
		}
		
		//dump($aDataActionPlan);
		
		return $aDataActionPlan;
	}

	
	private function getDataValuesCalculated($sCurrentDay, $idActionPlan)
	{
	/*	dump($sCurrentDay);
		$ret = array();
		$calculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($sCurrentDay, $idActionPlan);
		dump($calculation);
		if($calculation != null)
		{
			if(Count($calculation)> 0)
			{
				$idCalculation=$calculation[0]->getId();
			
				$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $sCurrentDay);
				$currentDay=$currentDay->modify(" -7 day")->format("Y-m-d H:i:s");	;
				dump($currentDay);
				$output = $this->em->getRepository('OptimusOptimusBundle:APPVMOutput')->findResgisterOutputsByDate($idCalculation, $currentDay);
				dump($output);
				// to check -> service return alerts in future (without historical data this seems not correct)
				
				if($output != null){
					if(Count($output) > 0){
						foreach($output as $aOutput){
							$ret[] = array( "hour"=>$aOutput->getHour(),
											"alarmPower"=>$aOutput->getAlarmPower(),
											"pvProduced"=>$aOutput->getPvproduced(),	
											"pvPredicted"=>$aOutput->getPvpredicted(),
											"dayAlert"=>$aOutput->getDayalert());
						}
						
						return $ret;
					}
				}
			}
		}   */
		
		
		//dump($sCurrentDay);
		$ret = array();
		$output = $this->em->getRepository('OptimusOptimusBundle:APPVMOutput')->findOutputsByDate($idActionPlan, $sCurrentDay);
		// to check -> service return alerts in future (without historical data this seems not correct)
		
		if($output != null){
			if(Count($output) > 0){
				foreach($output as $aOutput){
					$ret[] = array( "hour"=>$aOutput->getHour(),
									"alarmPower"=>$aOutput->getAlarmPower(),
									"pvProduced"=>$aOutput->getPvproduced(),	
									"pvPredicted"=>$aOutput->getPvpredicted(),
									"dayAlert"=>$aOutput->getDayalert());
				}
				
				return $ret;
			}
		}
		
		return null; // Not defined
	}

	private function getDifferenceDay($production_historical, $production_predicted, $zero_padding)
	{
		$production_difference=array();
		if($zero_padding){
			for($i=0; $i<24; $i++) {
				$production_difference[]=0; //"not available";
			}
		} else {
			for($i=0; $i<24; $i++) {
				$production_difference[]=$production_predicted[$i] - $production_historical[$i];
			}
		}
		
		return $production_difference;
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
	
	private function getTotal($aDataHistoric_day)
	{
		$total = 0;
		foreach($aDataHistoric_day as $aDataHistoric_value)	{
			$total = $total + $aDataHistoric_value;
		}
		return $total;
	}
	
	private function getDateString($from, $iDay)
	{
		$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		if(!$currentDay){
			$currentDay=\DateTime::createFromFormat('Y-m-d', $from);
		}
		return $currentDay->modify(" +".$iDay." day")->format("Y-m-d");
	}
	
	//GET Status & colors action plan	
	public function getTrafficLight($idActionPlan, $dateActual, $startDate, $endDate)
	{
		
		$initDay=$startDate->format("Y-m-d H:i:s");
		//$finalDay=$endDate->modify('-2 day')->format("Y-m-d H:i:s");
		$finalDay=$endDate->format("Y-m-d H:i:s");
		$actDay=$dateActual->format("Y-m-d H:i:s");
				
		$aDays=$this->getDaysFromDate($initDay, $finalDay);		
		$numDays=count($aDays);								
		$aDataActionPlan=array();
		$aFinalValues=array();
			
		
		for($i=0; $i < $numDays; $i++)
		{
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			$currentDayFormat=explode(" ", $aDays[$i])[0];
			
			
			if($qCalculation != null)
			{
				$idCalculation=$qCalculation[0]->getId();			
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVMOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{
					
					if($aDays[$i] < $actDay)
					{
						$status=$outputDay[0]->getStatus();
						if($status==0) 		$color="#ffff00";
						elseif($status==1)	$color="#00ff00";
						elseif($status==2)	$color="#ff0000";
						$aDataActionPlan[]=array("status"=>$color, "date"=>$aDays[$i]);			// 0=Unknown, 1=Accepted, 2=Declined 
					}elseif($aDays[$i] >= $actDay)
					{
						$status=$outputDay[0]->getStatus();
						if($status==0) 		$color="#cccccc";
						elseif($status==1)	$color="#00ff00";
						elseif($status==2)	$color="#ff0000";
						
						$aDataActionPlan[]=array("status"=>$color, "date"=>$aDays[$i]);
					}
				}else{					
					$aDataActionPlan[]=array("status"=>"#ffff00", "date"=>$aDays[$i]);
				}
			}else{
				$aDataActionPlan[]=array("status"=>"#ffff00", "date"=>$aDays[$i]);
			}
		}
		
		$numUnk=$this->calculateUnknowns($aDataActionPlan, $dateActual);
		
		//if($numUnk == 0) 		$strStatus=0;
		if($numUnk > 1)			$strStatus=1;
		else					$strStatus=2;
		
		$aFinalValues[]=array("aOutputActionPlan"=>$aDataActionPlan, "status"=>$strStatus);
		
		//dump($aFinalValues);
		
		return $aFinalValues;
		
	}
	
	//Get number of unknows 
	private function calculateUnknowns($aDataActionPlan, $dateActual)
	{
		$actDay=$dateActual->format("Y-m-d");
		$numUnk=0;
		foreach($aDataActionPlan as $dayActionPlan)
		{
			$currentDay=explode(" ", $dayActionPlan['date'])[0];
			
			//dump($currentDay);
			//dump($actDay);
			
			if($currentDay <= $actDay and $dayActionPlan['status']=="#ff0000")
			{
				//dump("mas pequeño y rojo");
				$numUnk++;
			}
			
		}
		
		return $numUnk;
	}

	//Get Status week 
	public function getStatusWeek($idActionPlan, $startDate, $endDate)
	{
		$initDay=$startDate." 00:00:00";
		$finalDay=\DateTime::createFromFormat('Y-m-d H:i:s', $endDate." 00:00:00")->modify("+1 day")->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($initDay, $finalDay);		
		$numDays=count($aDays);
		$aStatusWeek=array();

		for($i=0; $i < $numDays; $i++)
		{
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			$currentDayFormat=explode(" ", $aDays[$i])[0];
			
			
			if($qCalculation != null)
			{
				$idCalculation=$qCalculation[0]->getId();			
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVMOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{
					$aStatusWeek[]=array('status'=>$outputDay[0]->getStatus(), 'idOutputDay'=>$outputDay[0]->getId());
					
				}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
				
			}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
		}
		
		return $aStatusWeek;
	}
}

?>

 