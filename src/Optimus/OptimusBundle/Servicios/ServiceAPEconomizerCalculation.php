<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;

use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APEconomizerOutput;
use Optimus\OptimusBundle\Entity\APEconomizerOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;

class ServiceAPEconomizerCalculation
{
	// Members ------------------------------------------------------------------------------------
	
	// Static members:
    protected $em;
	protected $ontologia;
				
	private static $sensor_1_temperature_name = "Temperature (C)";	
	private static $sensor_2_humidity_name = "Humidty (%)";	
	
	private static $temperature_internal = 25.00;
	private static $enthalpy_internal = 55.50;
	
    //to be updated
	public function getTemperatureName(){return self::$sensor_1_temperature_name;}
	public function getHumidityName(){return self::$sensor_2_humidity_name;}
	
	
	// variable names:	
	private static $datetime_name = "datetime";
	private static $date_name = "date";
	
	
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
		//dump($aoRelSensorsActionPlan);
		
		
		$start=\DateTime::createFromFormat('Y-m-d H:i:s', $from)->modify(" -1 hour")->format("Y-m-d H:i:s");
		
		// 1.Init data structure:	
		$from = $this->getDateString($from, 0);
       
		$sCurrentDatetime = $from;
		//dump("From: ");
		//dump($sCurrentDatetime);
		
		$aValues=array("historical" => null, "predicted" => null, "hour" => null);
		$window = 169;
		$horizon = 169;
		$nDays = 7;
		
	
		// 2.Get the AP sensors:	
		$sensors = ""; 
		$temperature_sensor=""; 
		$humidity_sensor="";
		foreach($aoRelSensorsActionPlan['sensors'] as $sensors_partition)
        {
			foreach($sensors_partition as $apsensor){	
				switch ($apsensor->getName()){
					case self::$sensor_1_temperature_name:
						$temperature_sensor = $apsensor->getFkSensor()->getId();
						break;
					case self::$sensor_2_humidity_name:
						$humidity_sensor = $apsensor->getFkSensor()->getId();
						break;
				}
			}
			$sensors = $temperature_sensor."_".$humidity_sensor;
		}
		
		// 3. Get sensors predicted data:
		$startR = substr_replace($start,"T",10,1);
		$startR = $startR."Z";
		
		$building=$this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		switch ($building->getCity())
		{
			case "Sant Cugat":
				$url = "http://optimusdss.epu.ntua.gr/santcugat/semantic-framework/get_data_for_forecasting/production/".$startR."/".$window."/".$sensors;
				break;
		}
		
		
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/html')); 
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		//dump($response);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		
		// final text to parse:
		$body = substr($response, $header_size);			
		
		//dump($body);
		curl_close($ch);
		
		
		// 4. Put sensors data to DB:
		//if(OK)
			$result = explode("\n",$body);
			//dump(count($result));
			for($i=1; $i<count($result)-1; $i++){
				$resultHour = explode(";",$result[$i]);
				$temperature = $resultHour[0];
				$humidity = $resultHour[1];
				$shour = $resultHour[2];
				$shour = str_replace("T"," ",$shour);
				$shour = str_replace("Z","",$shour);
				$hour = \DateTime::createFromFormat('Y-m-d H:i:s', $shour);
				//dump($result[$i]);
				
				$output = new APEconomizerOutput();
				$output->setFkApCalculation($calculation);	
				$output->setHour($hour);							
				$output->setTemp_external($temperature);
				$output->setHumidity($humidity);
				
				$this->em->persist($output);
				$this->em->flush();
				
			}
			
			for($iDay=0; $iDay<$nDays; $iDay++)
			{
				$sCurrentDate = $this->getDateString($from, $iDay);
				$currentDate=\DateTime::createFromFormat('Y-m-d', $sCurrentDate );

				// 4.To manage inputs from users:	
				$outputDay = new APEconomizerOutputDay();
				$outputDay->setDate($currentDate);
				
				$lastOutputDay = $this->em->getRepository('OptimusOptimusBundle:APEconomizerOutputDay')->findLastOutputByDay($sCurrentDate); //
				if($lastOutputDay != null){
					$outputDay->setStatus($lastOutputDay[0]->getStatus());
				}
				else{
					$outputDay->setStatus(0);	
				}
				
				// Specific for this action plan:
				$outputDay->setTemp_internal(self::$temperature_internal);
				$outputDay->setEnth_internal(self::$enthalpy_internal);
				$outputDay->setFkApCalculation($calculation);

				$this->em->persist($outputDay);
				$this->em->flush();
			}
			
			
		//else
		// 	echo "Error invoking service\n\n </br></br>";
		
	}
	
	
	
	
	private function getDateString($from, $iDay)
	{
		$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		if(!$currentDay){
			$currentDay=\DateTime::createFromFormat('Y-m-d', $from);
		}
		return $currentDay->modify(" +".$iDay." day")->format("Y-m-d");
	}

	
	
	
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		
		$aVariablesInput[].=self::$sensor_1_temperature_name;
		$aVariablesInput[].=self::$sensor_2_humidity_name;
        
        
		return $aVariablesInput;
	}
}
?>