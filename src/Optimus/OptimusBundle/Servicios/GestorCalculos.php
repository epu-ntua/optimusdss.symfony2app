<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Servicios\GestorOntologia;
use Optimus\OptimusBundle\Servicios\GestorInvoke;
use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APPVOutput;
use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class GestorCalculos {
	
	protected $ontologia;
	
	protected $invoke;
 
    protected $em;
 
    public function __construct(GestorOntologia $ontologia, GestorInvoke $invoke, EntityManager $em)
    {
        $this->ontologia=$ontologia;
        $this->invoke=$invoke;
		$this->em=$em;
    }
	
	public function createPredictionAndCalculates($from='', $idBuilding)
	{
		$this->newPrediction($from, $idBuilding);
		$this->createCalculation($from, $idBuilding);
	}
	
	private function newPrediction($from='', $idBuilding)
	{
		$idPrediction=$this->createPrediction($from, $idBuilding);
		$aValues=$this->loadXml($from);
		$this->insertPredictions($aValues, $idPrediction);		
	}
	
	private function createPrediction($from='', $idBuilding)
	{
		$from=$from." 00:00:00";
		$dateCreate=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		//$dateCreate=new \DateTime(); //$from		
		$dateUser=new \DateTime();
		
		//dump($dateCreate);
		//dump($dateUser);
		
		$namePrediction="Prediction_".$dateCreate->format('Y-m-d H:i:s')."_".$dateUser->format('Y-m-d H:i:s');
		
		$building=$this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		
		$prediction = new Prediction();
		$prediction->setName($namePrediction);
		$prediction->setDateCreate($dateCreate);
		$prediction->setDateUser($dateUser);
		$prediction->setFkBuilding($building);
	 	
		$this->em->persist($prediction);
		$this->em->flush();	
		
		return $prediction->getId();
		
	}
	
	private function loadXml($from)
	{
		/*$container = new ContainerBuilder();
		$loader = new XmlFileLoader($container, new FileLocator('C:\xampp\htdocs\Symfony\src\Optimus\OptimusBundle\Resources\file')); //new FileLocator(__DIR__)
		$loader->load('predictradiationall.xml');
		//dump($loader);*/
		
		//$xml = simplexml_load_file('C:\xampp\htdocs\Symfony\src\Optimus\OptimusBundle\Resources\file\predictradiationall.xml');
		$startDate=$from."T00:00:00Z";
		set_time_limit(0);
		$prevXml=$this->invoke->predict_data('power', $startDate);	

		//dump($prevXml);
		
		$xml = simplexml_load_string($prevXml);
			
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
					$aValues[]=array("value"=>(int)$element->fit, "dateElement"=>$actDay);
					
					$j++;
				}
			}			
			$i++;
		}
		
		return $aValues;
	}
	
	private function insertPredictions($aValues, $idPrediction)
	{		
		$prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->find($idPrediction);
		$sensor=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findOneByName("Energy production");
		
		foreach($aValues as $element)
		{	
			//dump($element['date']);
			$register = new RegisterPredictions();
			$register->setDate($element['dateElement']);
			$register->setValue($element['value']);
			$register->setFkSensor($sensor);
			$register->setFkPrediction($prediction);
		 	
			$this->em->persist($register);
			$this->em->flush();
		}		
	}
  
	public function createCalculation($from='', $idBuilding)
	{
		//Action plans activos status=1
		$aActionsPlans=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("status"=>1, "fk_Building"=>$idBuilding));
		
		//Variables action plans
		$aSensorsActionPlan=$this->getSensorsByActionPlan($aActionsPlans);
		
		//Para cada Action se crea un nuevo calculo
		$idCalculation=$this->createNewCalculation($aSensorsActionPlan, $from);
		
		//Para los sensores del action plan se pide la ultima prediccion
		$this->insertOutputsActionPlan($aSensorsActionPlan, $from, $idCalculation, $idBuilding);
		//Se registra en la tabla del action plan estos sensores		
	}	
	
	private function getSensorsByActionPlan($aActionsPlans)
	{
		$aActionPlan=array();
		foreach($aActionsPlans as $actionPlan)
		{			
			$aSensors=array();			
			$sensors=$this->em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_actionplan"=>$actionPlan->getId()));
				
			foreach($sensors as $sensor)
			{
				$actSensor=$this->em->getRepository('OptimusOptimusBundle:Sensor')->find($sensor->getFkSensor());
				$aSensors[]=$actSensor->getId();
			}
			//dump($actionPlan->getSensors());
			$aActionPlan[]=array("id"=>$actionPlan, "sensors"=>$aSensors);
		}
		
		//dump($aActionPlan);
		return $aActionPlan;
		
	}

	private function createNewCalculation($aActionsPlans, $from)
	{
		$from=$from." 00:00:00";
		$dateCreate=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		//$dateCreate=new \DateTime(); //$from		
		
		
		foreach($aActionsPlans as $actionPlan)
		{
			//dump($actionPlan);
			$dateUser=new \DateTime();
			
			$newCalculation = new APCalculation();
			$newCalculation->setFkActionplan($actionPlan['id']);
			$newCalculation->setStartingDate($dateCreate);
			$newCalculation->setDateCreation($dateUser);	 
			
			$this->em->persist($newCalculation);
			$this->em->flush();
		}
		
		return $newCalculation->getId();
	}

	private function insertOutputsActionPlan($aActionsPlans, $from, $idCalculation, $idBuilding)
	{
		$from=$from." 00:00:00";
		$calculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->find($idCalculation);
		
		//dump($idCalculation);
		
		foreach($aActionsPlans as $actionPlan)
		{	
			//dump($actionPlan);
			//dump($actionPlan['id']->getType());
			if($actionPlan['id']->getType()==4) $this->insertPVOutput($actionPlan, $from, $calculation, $idBuilding);
			//if($actionPlan['id']->getType()==2) $this->insertSwitchOutput($actionPlan, $from, $calculation, $idBuilding);
			
			/*foreach($actionPlan['sensors'] as $sensor)
			{				
				//valuesSensor=getRegisterPrediction by date and sensor
				for($i=0; $i<7; $i++)
				{
					if($i>0)
					{
						$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
						$dateWeek=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
					}else $dateWeek=$from;
					
					$prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($dateWeek, $idBuilding);	//prediction
					//get registers prediction
					//dump($prediction);
					if(!empty($prediction))
					{
						$registerPrediction=$this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate($sensor, $prediction[0]->getId(), $dateWeek);
						
					}else $registerPrediction=array();
					
					foreach($registerPrediction as $register)
					{
						$output = new APPVOutput();
						$output->setHour($register->getDate());
						$output->setEnergyPrice(1.5);
						$output->setEnergyProduction($register->getValue());
						$output->setFkApCalculation($calculation);
					 
						
						$this->em->persist($output);
						$this->em->flush();
					}									
					
					//insert strategy & status of day
					$day=\DateTime::createFromFormat('Y-m-d', $dateWeek);
					
					$outputDay = new APPVOutputDay();
					$outputDay->setDate($day);
					$outputDay->setStrategy('Financial');
					$outputDay->setStatus(0);
					$outputDay->setFkApCalculation($calculation);
				 
					
					$this->em->persist($outputDay);
					$this->em->flush();
				}				
			}*/
		}
	}
	
	private function insertPVOutput($actionPlan, $from, $calculation, $idBuilding)
	{
		//dump($actionPlan);
		foreach($actionPlan['sensors'] as $sensor)
		{				
			//valuesSensor=getRegisterPrediction by date and sensor
			for($i=0; $i<7; $i++)
			{
				if($i>0)
				{
					$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
					$dateWeek=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
				}else $dateWeek=$from;
				
				$prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($dateWeek, $idBuilding);	//prediction
				//get registers prediction
				//dump($prediction);
				if(!empty($prediction))
				{
					$registerPrediction=$this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate($sensor, $prediction[0]->getId(), $dateWeek);
					
				}else $registerPrediction=array();
				
				//dump($registerPrediction);
				
				foreach($registerPrediction as $register)
				{
					$output = new APPVOutput();
					$output->setHour($register->getDate());
					$output->setEnergyPrice(1.5);
					$output->setEnergyProduction($register->getValue());
					$output->setFkApCalculation($calculation);
				 
					
					$this->em->persist($output);
					$this->em->flush();
				}									
				
				//insert strategy & status of day
				//dump($dateWeek);
				$date=explode(" ", $dateWeek);
				$day=\DateTime::createFromFormat('Y-m-d', $date[0]);
				
				//dump($day);
				
				$outputDay = new APPVOutputDay();
				$outputDay->setDate($day);
				$outputDay->setStrategy('Financial');
				$outputDay->setStatus(0);
				$outputDay->setFkApCalculation($calculation);
			 
				
				$this->em->persist($outputDay);
				$this->em->flush();
			}				
		}
	}
	
	private function insertSwitchOutput($actionPlan, $from, $calculation, $idBuilding)
	{
		foreach($actionPlan['sensors'] as $sensor)
		{
			for($i=0; $i<7; $i++)
			{
				if($i>0)
				{
					$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
					$dateWeek=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
				}else $dateWeek=$from;
				
				$prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($dateWeek, $idBuilding);	//prediction
				//get registers prediction
				//dump($prediction);
				if(!empty($prediction))
				{
					$registerPrediction=$this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate($sensor, $prediction[0]->getId(), $dateWeek); //get Predictions Temperatura externa
					
				}else $registerPrediction=array();
				
			//	dump($registerPrediction);
				
				foreach($registerPrediction as $register)
				{
					//total registros del dia X					
				}
				
				//calcula heating start
				//calcula heating stop
				//calcula cooling start
				//calcula cooling stop
				
				//inserta en switch_output
					//typeVariable (heating/cooling)
					//hour-start
					//hour-stop
					//day
					//fk_apcalculation
					//type Selected/Baseline ?????
			}
		}
	}
}

?>
