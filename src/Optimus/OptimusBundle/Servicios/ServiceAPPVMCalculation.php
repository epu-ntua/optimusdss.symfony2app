<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;


use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APPVMOutput;
use Optimus\OptimusBundle\Entity\APPVMOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;

class ServiceAPPVMCalculation
{
	// Members ------------------------------------------------------------------------------------
	
	// Static members:
    protected $em;
	protected $ontologia;
				
	//private static $sensor_1_temperature_name = "Temperature (C)";	
	//private static $sensor_2_humidity_name = "Humidty (%)";	
	//private static $sensor_3_windspeed_name = "Wind speed( KMh)";	
    //private static $sensor_4_winddirection_name = "Wind direction (Degrees)";	
    //private static $sensor_5_pressure_name = "Pressure (Pa)";	
	//private static $sensor_6_solarradiation_name = "solar radiation (Watts/m2)";	
	//private static $sensor_7_cloudcover_name = "Cloud cover (%)";	
    //private static $sensor_8_rainfall_name = "Rain fall (mm)";	
    //private static $sensor_9_snowfall_name = "Snow fall (cm)";	
    //private static $sensor_10_weathercondition_name = "Weather condition (W/m2)";	
    //private static $sensor_11_confidencelevel_name = "Confidence level (%)";	
    //private static $sensor_12_energyproduction_name = "Energy production (Kwh)";
	
	
	private static $sensor_6_solarradiation_name = "solar radiation (Watts/m2)";	
	private static $sensor_12_energyproductionph1_name = "Energy production ph_1 (Kwh)";
	private static $sensor_13_energyproductionph2_name = "Energy production ph_2 (Kwh)";
	private static $sensor_14_energyproductionph3_name = "Energy production ph_3 (Kwh)";	

	private static $sensor_6_solarradiation_color = "#01d98e";	
	private static $sensor_12_energyproductionph1_color = "#8900e9"; 
	private static $sensor_13_energyproductionph2_color = "#9f9d9e"; 
	private static $sensor_14_energyproductionph3_color = "#f39a02";

    //to be updated
	//public function getTemperatureName(){return self::$sensor_1_temperature_name;}
	//public function getHumidityName(){return self::$sensor_2_humidity_name;}
	//public function getPressureName(){return self::$sensor_3_pressure_name;}
	//public function getWindDirectionName(){return self::$sensor_4_winddirection_name;}
	//public function getSolarRadiationName(){return self::$sensor_5_solarradiation_name;}
	//public function getDewPointName(){return self::$sensor_6_dewpoint_name;}
	//public function getWindSpeedName(){return self::$sensor_7_windspeed_name;}
	//public function getEnergyProductionName(){return self::$sensor_12_energyproduction_name;}
	
	
	
	public function getSolarRadiationName(){return self::$sensor_6_solarradiation_name;}
	public function getEnergyProductionPh1Name(){return self::$sensor_12_energyproductionph1_name;}
	public function getEnergyProductionPh2Name(){return self::$sensor_13_energyproductionph2_name;}
	public function getEnergyProductionPh3Name(){return self::$sensor_14_energyproductionph3_name;}

	
	// variable names:	
	private static $datetime_name = "datetime";
	private static $date_name = "date";
	private static $alarmPower_name = "alarm_power";
	private static $pvproduced_name = "PVproduced";
	private static $pvpredicted_name = "PVpredicted";
    private static $pvdifference_name = "PVdifference";
	
	protected $invokePredictData; 
	protected $events; 
	
	// Constructors -------------------------------------------------------------------------------
	
    public function __construct(EntityManager $em,
								ServiceOntologia $ontologia,
								ServicePredictDataInvoke $invokePredictData,
								ServiceEvents $events)
    {       
		$this->em=$em;
		$this->ontologia=$ontologia;
		$this->invokePredictData=$invokePredictData;
		$this->events=$events;
    }	
	
	public function insertNewCalculation($aoRelSensorsActionPlan, $from,  $calculation, $idBuilding, $ip, $user)
	{
		// Obs: 
		// - There is no prediction data for this action plan.
		// - This calculation need be done here because web services are only invoked one time in the night.
		
		$actionPlan = $aoRelSensorsActionPlan['actionPlan'];
		
		$start=\DateTime::createFromFormat('Y-m-d H:i:s', $from)->modify(" -1441 hour")->format("Y-m-d H:i:s");
		
		// 1.Init data structure:	
		$from = $this->getDateString($from, 0);
		$sCurrentDatetime = $from;
		
		$aValues=array("historical" => null, "predicted" => null, "hour" => null);
		$window = 1441;
		$horizon = 1441;
		$nDays = 7;
		
		// We are getting the sensors ID for sending them to the rapidminer service.
		$sensors = ""; 
		$radiation_sensor=""; 
		$productionph1_sensor="";
		$productionph2_sensor="";
		$productionph3_sensor="";
		foreach($aoRelSensorsActionPlan['sensors'] as $sensors_partition)
        {
			foreach($sensors_partition as $apsensor){	
				switch ($apsensor->getName()){
					case self::$sensor_6_solarradiation_name:
						$radiation_sensor = $apsensor->getFkSensor()->getId();
						break;
					case self::$sensor_12_energyproductionph1_name:
						$productionph1_sensor = $apsensor->getFkSensor()->getId();
						break;
					case self::$sensor_13_energyproductionph2_name:
						$productionph2_sensor = $apsensor->getFkSensor()->getId();
						break;
					case self::$sensor_14_energyproductionph3_name:
						$productionph3_sensor = $apsensor->getFkSensor()->getId();
						break;
				}
			}
			$sensors = $productionph1_sensor."_".$productionph2_sensor."_".$productionph3_sensor."_".$radiation_sensor;
			$sensors = str_replace("__", "", $sensors);
		}
		
		//echo "Calculating PV Maintenance action plan from ".$start."\n\n </br></br>";     

		$building=$this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		switch ($building->getCity())
		{
			case "Savona":
				if (strpos($building->getName(), 'School') !== false){
					//dump($building->getName());
				$url = "http://optimusdss.epu.ntua.gr:8080/RAWS/process/Savona/ActionPlan_PV_maintenance_R_School";}
				elseif (strpos($building->getName(), 'Campus') !== false)
					$url = "http://optimusdss.epu.ntua.gr:8080/RAWS/process/Savona/ActionPlan_PV_maintenance_R_Campus";
				break;	
				
			case "Sant Cugat":
				switch ($building->getName())
				{	
					default:
						$url = "http://optimusdss.epu.ntua.gr:8080/RAWS/process/Santcugat/ActionPlan_PV_maintenance_R";
				}
				break;
		}
		
		$startR = substr_replace($start,"T",10,1);
		$startR = $startR."Z";
		
		// 2.1.Get values about: 1.Alarms and 2.Power: Prediction (Web Service)	
		$prevXml = $this->invokePredictData->PredictData($url, $startR, $window, $horizon, $sensors);
		
		libxml_use_internal_errors(true);
		//dump($prevXml);
		$xml = simplexml_load_string($prevXml);
		//dump($xml);
		if($xml!==false){
			$aValues = $this->readXML_Maintenance($xml);
		} else {
			echo "Error invoking service\n\n </br></br>";
		}
		
        //dump($aValues);
		
		$iHourStart = 0;
		$lastCalculations = $this->em->getRepository('OptimusOptimusBundle:APCalculation')->findLastCalculationWithPVMOutput($actionPlan->getId());
		if(count($lastCalculations) > 0){
			$lastCalculationID = $lastCalculations[0]->getId();
			$lastPVMOutputHour = $this->em->getRepository('OptimusOptimusBundle:APPVMOutput')->findLastOutputByCalculation($lastCalculationID)[0]->getHour();
			$startingHour = $lastPVMOutputHour->modify(" + 1 hour")->format("Y-m-d H:i:s");;			

			$t1 = StrToTime ( $start );
			$t2 = StrToTime ( $startingHour );
			$diff = $t2 - $t1;
			$iHourStart = $diff / ( 60 * 60 ) ;
		}
		if($iHourStart<0){
			$iHourStart = 0;
		}
		
		$finalHour = count($aValues['hour']);
		$cntr = 1;
		while ($aValues['hour'][count($aValues['hour']) - $cntr][self::$pvproduced_name] == 0){
			if($cntr%24 == 0){
				$finalHour -= 24;
			}
			$cntr++;
		}
		
		
		$this->em->getConnection()->beginTransaction();
		// 3.Insert values on DB (APPVMOutput): -> for the previous week
		// Try and make the transaction
		try {   
			for($iHour=$iHourStart; $iHour<$finalHour; $iHour++)
			{
				$value = $aValues['hour'][$iHour];

				$output = new APPVMOutput();
				$output->setFkApCalculation($calculation);	
				
				$value[self::$datetime_name] = str_replace("T"," ",$value[self::$datetime_name]);
				$value[self::$datetime_name] = str_replace("Z","",$value[self::$datetime_name]);

				$dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $value[self::$datetime_name]);
				$output->setHour($dateTime);
							
				$output->setAlarmPower($value[self::$alarmPower_name]);
				$output->setPvproduced($value[self::$pvproduced_name]);
				$output->setPvpredicted($value[self::$pvpredicted_name]);
				
				$dayValue = $aValues['historical'][($iHour)/24];	
				$output->setDayalert($dayValue['alert']);
				$this->em->persist($output);
				$this->em->flush();
				
				//Events alarms ==1
			/*	if($value[self::$alarmPower_name]==1)
				{
					$dateAlarm=$value[self::$datetime_name];				
					$text="Alarm Power active ".$dateAlarm->format('Y-m-d H:i:s');
					
					$this->events->createEvent($user, $text , "Action Plan -".$actionPlan->getName()."", $actionPlan->getId(), 1, $ip, $idBuilding, "alarm power");
				}
				if($value[self::$alarmTemperature_name]==1)
				{
					$dateAlarm=$value[self::$datetime_name];
					$text="Alarm Temperature active ".$dateAlarm->format('Y-m-d H:i:s');
					
					$this->events->createEvent($user, $text, "Action Plan -".$actionPlan->getName()."", $actionPlan->getId(), 1, $ip, $idBuilding, "alarm temperature");
				}*/
			}
			
			for($iDay=0; $iDay<$nDays; $iDay++)
			{
				$sCurrentDate = $this->getDateString($from, $iDay);
				$currentDate=\DateTime::createFromFormat('Y-m-d', $sCurrentDate );

				// 4.To manage inputs from users:	
				$outputDay = new APPVMOutputDay();
				$outputDay->setDate($currentDate);
				
				$lastOutputDay = $this->em->getRepository('OptimusOptimusBundle:APPVMOutputDay')->findLastOutputByDay($sCurrentDate); //
				//dump($lastOutputDay);
				if($lastOutputDay != null){
					$outputDay->setStatus($lastOutputDay[0]->getStatus());
				}
				else{
					$outputDay->setStatus(0);	
				}
				
				$outputDay->setAlert($aValues['predicted'][$iDay]['alert']);		// Predicted Alert 
				$outputDay->setFkApCalculation($calculation);

				$this->em->persist($outputDay);
				$this->em->flush();
			}
			
			// Try and commit the transaction
			$this->em->getConnection()->commit();
		}catch (\Exception $e) {
			// Rollback the failed transaction attempt
			$this->em->getConnection()->rollback();
			//$this->em->getRepository('OptimusOptimusBundle:APCalculation')->deleteCalculationById($calculation);
			$apcalculation = $this->em->getRepository('OptimusOptimusBundle:APCalculation')->find($calculation);
			$this->em->remove($apcalculation);
			$this->em->flush();
			throw $e;
		}
		
	}
	
	
	
	private function readXML_Maintenance($xml)
	{
		$aValues = array();
		foreach($xml  as $result){
			$RResult = str_replace("\r\n", " ", $result->RResult[0]);
			strtok($RResult, " ");$alertName = strtok(" ");
			$id = strtok(" ");
			$historicalValues = array();
			while($id !== false){
				$date = strtok(" ");
				$alert = strtok(" ");
				$aValue = array("date" => $date, "alert" => $alert);
				
				$historicalValues[] = $aValue;
				
				$id = strtok(" ");
			}
			
			$aValues['historical'] = $historicalValues;
			
			$RResult = str_replace("\r\n", " ", $result->RResult[1]);
			strtok($RResult, " ");strtok(" ");strtok(" ");
			$id = strtok(" ");
			$predictedValues = array();
			while($id !== false){
				$day = strtok(" ");
				$est_risk = strtok(" ");
				$alert = strtok(" ");
				$aValue = array("day" => $date, "est_risk" => $est_risk, "alert" => $alert);
				
				$predictedValues[] = $aValue;
				
				$id = strtok(" ");
			}
			
			$aValues['predicted'] = $predictedValues;
			
			$RResult = str_replace("\r\n", " ", $result->RResult[2]);
			strtok($RResult, " ");strtok(" ");strtok(" ");strtok(" ");strtok(" ");strtok(" ");
			$id = strtok(" ");
			$hourValues = array();
			while($id !== false){
				$pv_predicted = strtok(" ");
				strtok(" "); strtok(" ");
				$timestamp = strtok(" ");
				$pv_historical = strtok(" ");
				$alarm = strtok(" ");
				$aValue = array(self::$pvpredicted_name => $pv_predicted, self::$datetime_name => $timestamp, self::$pvproduced_name => $pv_historical, self::$alarmPower_name => $alarm);
				
				$hourValues[] = $aValue;
				
				$id = strtok(" ");
			}
			
			$aValues['hour'] = $hourValues;
			
		}
		
		return $aValues;
	}
	
	
	
	private function getDateString($from, $iDay)
	{
		$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		if(!$currentDay){
			$currentDay=\DateTime::createFromFormat('Y-m-d', $from);
		}
		return $currentDay->modify(" +".$iDay." day")->format("Y-m-d");
	}

	
	
	public function getDataVariablesInput($buildingName)
	{
		$aVariablesInput=array();
		
   		if (strpos($buildingName, 'School') !== false){ 
			$aVariablesInput[].=self::$sensor_14_energyproductionph3_name;	
			$aVariablesInput[].=self::$sensor_13_energyproductionph2_name;	
		}
		$aVariablesInput[].=self::$sensor_12_energyproductionph1_name;
		$aVariablesInput[].=self::$sensor_6_solarradiation_name;
        
		return $aVariablesInput;
	}
	
	public function getColorsVariables()
	{
		$data = array();
		$data[self::$sensor_6_solarradiation_name] = self::$sensor_6_solarradiation_color;
		$data[self::$sensor_12_energyproductionph1_name] = self::$sensor_12_energyproductionph1_color;
		$data[self::$sensor_13_energyproductionph2_name] = self::$sensor_13_energyproductionph2_color;
		$data[self::$sensor_14_energyproductionph3_name] = self::$sensor_14_energyproductionph3_color;
		
		return $data;
	}
}
?>