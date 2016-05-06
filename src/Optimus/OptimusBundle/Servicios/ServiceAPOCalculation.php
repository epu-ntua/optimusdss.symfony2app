<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;

use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;
use Optimus\OptimusBundle\Entity\APOOutputDay;
use Optimus\OptimusBundle\Entity\Sensor;
//use Optimus\OptimusBundle\Entity\APOOutput;

class ServiceAPOCalculation {
 
    protected $em;
 
	private static $sensor_energyConsumption_name = "Energy Consumption";

	//private static $sensor_indoorTemperature_name = "Indoor Temperature";
	//private static $sensor_outdoorTemperature_name = "Outdoor Temperature";
	//private static $sensor_humidity_name = "Humidity";
	//private static $sensor_userFeedback_name = "User Feedback";

	//public function getIndoorTemperatureName(){return self::$sensor_indoorTemperature_name;}
	//public function getOutdoorTemperatureName(){return self::$sensor_outdoorTemperature_name;}
	//public function getHumidityName(){return self::$sensor_humidity_name;}
	//public function getUserFeedbackName(){return self::$sensor_userFeedback_name;}
	
	public function getEnergyProductionName(){return self::$sensor_energyConsumption_name;}
 
    public function __construct(EntityManager $em)
    {       
		$this->em=$em;
    }
	
	public function insertNewCalculation($aoRelSensorsActionPlan, $from,  $calculation, $idBuilding, $ip, $user)
	{
		$actionPlan = $aoRelSensorsActionPlan['actionPlan'];
		$idAPType = $actionPlan->getId();

		// 1.Init data structure:
		$from = $this->getDateString($from, 0);
		echo $from . '\n';

		$start_date = new \DateTime($from);

		$nDays = 7;

		echo "invoke service of the Occupancy inference rule\n";
		$this->invoke_service($idBuilding, $start_date->format('Y-m-d 00:00:00'), $idAPType, $calculation);

		for($iDay=0; $iDay<$nDays; $iDay++)
		{
			$sCurrentDate = $this->getDateString($from, $iDay);
			$currentDate=\DateTime::createFromFormat('Y-m-d', $sCurrentDate );

			$statusDay=$this->getLastCalculationDay($currentDate->format('Y-m-d'), $aoRelSensorsActionPlan['actionPlan']->getId());

			$outputDay = new APOOutputDay();
			$outputDay->setDate($currentDate);
			
			$lastOutputDay = $this->em->getRepository('OptimusOptimusBundle:APOOutputDay')->findLastOutputByDay($sCurrentDate);
			if($lastOutputDay != null){
				$outputDay->setStatus($lastOutputDay[0]->getStatus());
			}
			else{
				$outputDay->setStatus(0);	
			}

			// Specific for this action plan:
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
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APOOutputDay')->findOutputByDay($idCalculation, $dayFinal); //
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
		
		$aVariablesInput[].=self::$sensor_energyConsumption_name;
		
		return $aVariablesInput;
	}

	public function invoke_service($idBuilding, $start_date, $idAPType, $calculation) {
		return 0;
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