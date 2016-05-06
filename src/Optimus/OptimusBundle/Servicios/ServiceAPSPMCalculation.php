<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;

use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;
use Optimus\OptimusBundle\Entity\APSPMOutputDay;

class ServiceAPSPMCalculation {

	protected $em;
	protected $apadaptative;
	protected $aptcv;

	private static $sensor_indoorTemperature_name = "Indoor Temperature";
	private static $sensor_outdoorTemperature_name = "Outdoor Temperature";
	private static $sensor_humidity_name = "Humidity";
	private static $sensor_userFeedback_name = "User Feedback";

	public function getIndoorTemperatureName(){return self::$sensor_indoorTemperature_name;}
	public function getOutdoorTemperatureName(){return self::$sensor_outdoorTemperature_name;}
	public function getHumidityName(){return self::$sensor_humidity_name;}
	public function getUserFeedbackName(){return self::$sensor_userFeedback_name;}

	public function __construct(EntityManager $em, ServiceAPAdaptative $apadaptative, ServiceAPTCV $aptcv)
	{
		$this->em=$em;
		$this->apadaptative=$apadaptative;
		$this->aptcv=$aptcv;
	}

	public function insertNewCalculation($aoRelSensorsActionPlan, $from,  $calculation, $idBuilding, $ip, $user)
	{
		// Obs:
		// - There is no prediction data for this action plan.
		// - This calculation need be done here because web services are only invoked one time in the night.

		$actionPlan = $aoRelSensorsActionPlan['actionPlan'];
		$idAPType = $actionPlan->getType();

		// 1.Init data structure:
		$from = $this->getDateString($from, 0);
		echo $from . '\n';

		$start_date = new \DateTime($from);

		$nDays = 7;

		echo "invoke service of the Adaptive Comfort inference rule\n";
		//invoke service of the Adaptive Comfort inference rule
		$this->apadaptative->invoke_service($idBuilding, $start_date->format('Y-m-d 00:00:00'), $idAPType, $calculation);
		echo "invoke service of the TCV inference rule based on last week data\n";
		// invoke service of the TCV inference rule based on last week data
		//$this->aptcv->invoke_service($idBuilding, $start_date->format('Y-m-d 00:00:00'), $idAPType, $calculation);

		for($iDay=0; $iDay<$nDays; $iDay++)
		{
			$sCurrentDate = $this->getDateString($from, $iDay);
			$currentDate=\DateTime::createFromFormat('Y-m-d', $sCurrentDate );

			$statusDay=$this->getLastCalculationDay($currentDate->format('Y-m-d'), $aoRelSensorsActionPlan['actionPlan']->getId());

			$outputDay = new APSPMOutputDay();
			$outputDay->setDate($currentDate);

			$lastOutputDay = $this->em->getRepository('OptimusOptimusBundle:APSPMOutputDay')->findLastOutputByDay($sCurrentDate); //
			//dump($lastOutputDay);
			if($lastOutputDay != null){
				$outputDay->setStatus($lastOutputDay[0]->getStatus());
			}
			else{
				$outputDay->setStatus(0);	
			}
			
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
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSPMOutputDay')->findOutputByDay($idCalculation, $dayFinal); //
				if($outputDay)
				{
					$status=$outputDay[0]->getStatus();
				}
			}
		}
		return $status;
	}

	public function getPartitions($idBuilding)
	{
		$allPartitions=array();
		//$em = $this->getDoctrine()->getEntityManager();
		$partitionsBuilding=$this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		if($partitionsBuilding) {
			foreach ($partitionsBuilding as $partition) {
				$childs = $this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_BuildingPartitioning" => $partition->getId()));
				if ($childs)
					if ($partition->getFkBuildingPartitioning() == NULL) {
						$allPartitions[$partition->getPartitionName()] = array();
					}
					else {
						$father = $this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $partition->getFkBuildingPartitioning()));
						$sections = array();
						$sections[0] = $father;
						while ($sections[0]->getFkBuildingPartitioning() != NULL) {
							$father = $this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $sections[0]->getFkBuildingPartitioning()));
							array_unshift($sections, $father);
						}
						$partitions = &$allPartitions;
						for ($i=0; $i<count($sections); $i++) {
							$partitions = &$partitions[$sections[$i]->getPartitionName()];
						}
						$partitions[$partition->getPartitionName()] = array();
					}
				else {
					if ($partition->getFkBuildingPartitioning() != NULL) {
						$father = $this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $partition->getFkBuildingPartitioning()));
						$sections = array();
						$sections[0] = $father;
						while ($sections[0]->getFkBuildingPartitioning() != NULL) {
							$father = $this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $sections[0]->getFkBuildingPartitioning()));
							array_unshift($sections, $father);
						}
						$partitions = &$allPartitions;
						for ($i=0; $i<count($sections); $i++) {
							$partitions = &$partitions[$sections[$i]->getPartitionName()];
						}
						$partitions[] = $partition->getPartitionName();
					}
				}
			}
		}
		return $allPartitions;
	}

	public function getDataVariablesInput()
	{
		$aVariablesInput=array();

		$aVariablesInput[].=self::$sensor_indoorTemperature_name;
		$aVariablesInput[].=self::$sensor_outdoorTemperature_name;
		$aVariablesInput[].=self::$sensor_humidity_name;
		$aVariablesInput[].=self::$sensor_userFeedback_name;

		return $aVariablesInput;
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
