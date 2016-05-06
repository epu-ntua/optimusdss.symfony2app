<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;
use Optimus\OptimusBundle\Entity\APSwitchOutput;
use Optimus\OptimusBundle\Entity\APSwitchOutputDay;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;


class ServiceAPPreheating {
 
    protected $em;
	protected $ontologia;
	
	private static $sensor_1_indoor_temperature_name = "Indoor temperature (C)";	
	private static $sensor_2_outdoor_temperature_name = "Outdoor temperatue (C)";	
	private static $sensor_3_heating_setpoint_name = "Heating setpoint (C)";	
	
	private static $datetime_name = "datetime";
	private static $date_name = "date";
	private static $prediction_name = "prediction";
	private static $schedule_name = "Schedule";
	
		
	public function getIndoorTemperatureName(){return self::$sensor_1_indoor_temperature_name;}
	public function getOutdoorTemperatureName(){return self::$sensor_2_outdoor_temperature_name;}
	public function getHeatingSetpointName(){return self::$sensor_3_heating_setpoint_name;}
	
	
	
	protected $invokePredictData; 
	protected $events; 
	
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
	
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[].=self::$sensor_1_indoor_temperature_name;
		$aVariablesInput[].=self::$sensor_2_outdoor_temperature_name;
		$aVariablesInput[].=self::$sensor_3_heating_setpoint_name;
		
		return $aVariablesInput;
	}
	
	public function insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding)
    {
		// $aoRelSensorsActionPlan -> este array tiene una lista de sensores mapeados a particiones. Tabla APSensors. 
		// este action plann debe iterar por este array, y para cada particion diferente calcular el action plan en cuestion.
		// los calculos de cada particion se guardan:
		//   - apcalculation : 1 fila
		//   - apswitchoutput : 1 fila por celda de la tabla. Las columnas serian; fp_ap_calculation, fk...partition (fk), date (fecha), onheating (int), oncooling (int), offheating (int), offcooling (int)
		//   - apswitchoutputday: tabla para guardar que perfil de ocupacion se ha seleccionado por día y el status (accept, decline...) seleccionado por el usuario.


		
		// Obs: 
		// - There is no prediction data for this action plan.
		// - This calculation need be done here because web services are only invoked one time in the night.
		
		$actionPlan = $aoRelSensorsActionPlan['actionPlan'];
		//dump($aoRelSensorsActionPlan);
		
		// 1.Init data structure:	
		$from = $this->getDateString($from, 0);

        
        // 2. Move date to this monday.
        $from = date("Y-m-d", strtotime('monday this week'));   
		
        
		$sCurrentDatetime = $from;
		$nDays = 7;
		
		//dump($sCurrentDatetime);
		$aValues=array();
		$window = 169;
		
		$heatingsetpointSensor = -1;
		

		
		foreach($aoRelSensorsActionPlan['sensors'] as $partition_id=>$sensors_partition)
        {
            // We are getting the sensors ID for sending them to the rapidminer service.
            $sensors = "";
			//For each partition we are going to calculate the action plan
			foreach($sensors_partition as $apsensor) {

				
				
				if($apsensor->getName() == self::$sensor_3_heating_setpoint_name) {
					$heatingsetpointSensor = $apsensor->getId();

				} else {
                    $sensors.= $apsensor->getfkSensor()->getId()."_";
                }
			}

		    $sensors = substr($sensors, 0, -1);
            
			$partition = $this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->find($partition_id);

			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//Setting the context for selecting the proper prediciton model already calculated with the same context.
			// Hardcoded now!
			$context = "test".$partition_id."_";
			$context = "test30_";	//this should be removed

			//Set point hardcoded! we should take the value from a place.
			$setpoint = $this->getHeatingSetpoint($heatingsetpointSensor);	
            
            $fromSunday = date('Y-m-d', strtotime($from. ' - 3 days'));
			//




			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			echo "Calculating Swith On/Off Preheating action plan for partition: ".$partition_id." at ".$fromSunday."\n\n";






      
            
			// 2.1.Get values about: 1.Alarms and 2.Power: Prediction (Web Service)
			$prevXml = $this->invokePredictData->PredictDataSwitchOnOff("http://optimusdss.epu.ntua.gr:8080/RAWS/process/Santcugat/Forecasting_Prediction_model_Indoor_temperature_real", $fromSunday."T19:00:00Z", $window, $sensors, $setpoint, $context);
			
			libxml_use_internal_errors(true);
			
			$xml = simplexml_load_string($prevXml);
			
			if($xml!==false){
				$aOnOffDates = $this->readXML_Maintenance($xml, $from);

				//This returns an array of 5 days with the start and stop date.
				
				//dump($aOnOffDates);
				$i = 0;
				foreach($aOnOffDates  as  $key=>$date)
				{
					
					
					$date = str_replace("T", " ", $aOnOffDates[$key][2]);
					$date = str_replace("Z", "", $date);
								
					//$date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
					$date = \DateTime::createFromFormat('Y-m-d', $sCurrentDatetime);
					$date = $date->modify(" +".$i." day");
					
					
					echo "Day ".$date->format('Y-m-d').": ".$aOnOffDates[$key][0]." to ".$aOnOffDates[$key][1]." <br>";
					
					$output = new APSwitchOutput();
					$output->setFkApCalculation($calculation);	
					$output->setDay($date);
					$output->setStart($aOnOffDates[$key][0]);
					$output->setStop($aOnOffDates[$key][1]);
					$output->setSetpoint($setpoint);					
					$output->setFkBuildingPartitioning($partition);
					
					$this->em->persist($output);
					$this->em->flush();
					
					$i++;
				}
			}
			else {
				echo "Error invoking service\n";
				//dump($prevXml);
			}
		}
		
		//Insert --> Table APSwitchOutputDay
		for($iDay=0; $iDay<$nDays; $iDay++)
        {
			$sCurrentDate = $this->getDateString($from, $iDay);
            $currentDate=\DateTime::createFromFormat('Y-m-d', $sCurrentDate );
			$currentDateWithHour=\DateTime::createFromFormat('Y-m-d H:i:s', $sCurrentDate." 00:00:00" )->format('Y-m-d H:i:s');
			
			$statusDay=$this->getLastCalculationDay($currentDateWithHour, $aoRelSensorsActionPlan['actionPlan']->getId());

			// 4.To manage inputs from users:	
            $outputDay = new APSwitchOutputDay();
            $outputDay->setDate($currentDate);
			
			// Specific for this action plan:
            $outputDay->setStatus($statusDay);						// Alarm status by user
            $outputDay->setFkApCalculation($calculation);

            $this->em->persist($outputDay);
            $this->em->flush();
		}
		
	}

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
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutputDay')->findOutputByDay($idCalculation, $dayFinal); //
				if($outputDay)
				{				
					$status=$outputDay[0]->getStatus();						
				}
			}
		}
		return $status;
	}

	private function readXML_Maintenance($xml, $from) // $from, $idBuilding, $window, $urlService)
	{
		$aOnOffDates=array(1 => array("", "", ""), 2 => array("", "", ""), 3 => array("", "", ""), 4 => array("", "", ""), 5 => array("", "", ""));

		$i=0;

		foreach($xml  as  $result2){ 
			if($i < 1) {
				//day data
				foreach($result2  as  $key=>$element){ 
					
					if((string)$element->Schedule != "") {
						foreach($aOnOffDates  as  $key=>$date){ 
							
							if( strcmp ((string)$element->Schedule, "On".$key) == 0) {
								$aOnOffDates[$key][0] = (string)$element->datetime_hour;
								$aOnOffDates[$key][2] = (string)$element->datetime_old;
							}
							if( strcmp ((string)$element->Schedule, "Off".$key) == 0) {
								$aOnOffDates[$key][1] = (string)$element->datetime_hour;
								$aOnOffDates[$key][2] = (string)$element->datetime_old;
							}
						}
					}
				}
			} 

			$i++;
		}
		
		return $aOnOffDates;
	}
	
	private function getHeatingSetpoint($heatingsetpointSensor)
	{
     	//hradcoded to this temperature because the current heating setpoint is 26 and do not work well
		return 23.3;


        
		//If there is not a sensor defined then return a default value?? or the value set by another action plan.
		if($heatingsetpointSensor < 0 )
			return 23.2;
		

		$oSensor=$this->em->getRepository('OptimusOptimusBundle:Sensor')->find($heatingsetpointSensor);
		
		$rows = $aDataHistoric=$this->ontologia->checkSensor($oSensor->getUrl());
		
		if(count($rows) > 0)
			return $rows[0]["value"];
				
		return 23.2;
	}
	
	private function getDateString($from, $iDay)
	{
		$currentDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		if(!$currentDay){
			$currentDay=\DateTime::createFromFormat('Y-m-d', $from);
		}
		return $currentDay->modify(" +".$iDay." day")->format("Y-m-d");
	}

}
?>