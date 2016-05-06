<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

use Optimus\OptimusBundle\Entity\Building;
use Optimus\OptimusBundle\Entity\BuildingPartitioning;
use Optimus\OptimusBundle\Entity\ActionPlans;
use Optimus\OptimusBundle\Entity\APSensors;
use Optimus\OptimusBundle\Entity\BuildingSensorsRTime;

use Optimus\OptimusBundle\Form\BuildingType;
use Optimus\OptimusBundle\Form\APSensorsType;
use Optimus\OptimusBundle\Form\BuildingSensorsRTimeType;


class BuildingController extends Controller
{			
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
		$buildings=$em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		$dataBuildings=$this->getInformationByBuilding($buildings);
		
		return $this->render('OptimusOptimusBundle:Admin:adminBuildings.html.twig', array('buildings'=>$buildings, "dataBuildings"=>$dataBuildings));
    }
	
	//Data #partitions, #sensors, #actionPlan
	private function getInformationByBuilding($buildings)
	{
		$em = $this->getDoctrine()->getManager();
		$aDataBuilding=array();
		if($buildings)
		{
			foreach($buildings as $building)
			{
				
				$numPartitions=$numSensors=$numRTime=$numAP=0;
				
				//GetPartitions				
				$partitions=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$building->getId()));
				if($partitions) 	$numPartitions=(count($partitions));				
				
				//GetSensors
				$sensors=$em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array("fk_Building"=>$building->getId()));
				if($sensors) 		$numSensors=(count($sensors));
				else 				$numSensors=0;
				
				//GetActionPlans
				$actionPlans=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$building->getId()));
				if($actionPlans) 	$numAP=(count($actionPlans));
				else 				$numAP=0;
				
				//GetRTimeIndicators
				$bsensorsRTIme=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building"=>$building->getId()));
				if($bsensorsRTIme) 	$numRTime=(count($bsensorsRTIme));
				else 				$numRTime=0;
				
				$aDataBuilding[$building->getId()]=array("partitions"=>$numPartitions, "sensors"=>$numSensors, "aPlans"=>$numAP, "rTime"=>$numRTime);
			}
		}
		return $aDataBuilding;
	}

	public function newAction()
	{	
		$entity = new Building();
        $form   = $this->createForm(new BuildingType(), $entity);
		
		
        return $this->render('OptimusOptimusBundle:Building:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
	}
	
	public function createAction(Request $request)
	{
		$building  = new Building();       
        $form    = $this->createForm(new BuildingType(), $building);
		$form->handleRequest($request);

        if ($form->isValid()) {           
            $em = $this->getDoctrine()->getManager();
            $em->persist($building);
            $em->flush();
				
			$newBuilding=$em->getRepository('OptimusOptimusBundle:Building')->find($building->getId());
			//dump($newBuilding);
			
			//create partition 0 with buildingpartition=null
			$partition=new BuildingPartitioning();
			$partition->setPartitionName("Building");
			$partition->setFkBuilding($newBuilding);			
			$em->persist($partition);
			$em->flush();	
			
			//create Action plans for building
			$numActionPlans=$this->container->getParameter('optimus_actionPlans');
		
			for($i=1; $i<=$numActionPlans; $i++)
			{				
				$actionPlanActual=$this->container->getParameter("action_plan_".$i);
				$actionPlan=new ActionPlans();
				$actionPlan->setName($actionPlanActual['name']);
				$actionPlan->setStatus($actionPlanActual['status']);
				$actionPlan->setFkBuilding($newBuilding);
				$actionPlan->setDescription($actionPlanActual['description']);
				$actionPlan->setType($actionPlanActual['type']);
				//$newBuilding->addActionsPlan($actionPlan);
				
				$em->persist($actionPlan);
				$em->flush();	
				
			}
			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Create building", "Building", $building->getId(), 1, $ip, $building->getId(), "create" );

			return $this->redirect($this->generateUrl('adminBuilding'));            
		}

        /*return $this->render('OptimusOptimusBundle:Building:new.html.twig', array(
            'entity' => $building,
            'form'   => $form->createView()
        ));*/
		return ($this->newAction());
	}
	
	public function deleteAction($idBuilding)
	{
		$em = $this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		
		if($building)
		{
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Delete Building", "Building", $idBuilding, 1, $ip, $idBuilding, "delete" );
			
			//eliminar particiones
			$partitionsBuilding=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
			if($partitionsBuilding)
			{
				foreach($partitionsBuilding as $partition)
				{
					if($partition->getFkBuildingPartitioning()!=null)
					{
						//Cuando se elimina sus hijos tb se eliminan
						$em->remove($partition);
						$em->flush();

					}else{ //Building
						//Eliminamos todos sus hijos
						$childs = $partition->getChildren();				
						foreach ($childs as $child )
						{					
							$partition->removeChild($child);
							$em->flush();
						}			
					}
				}
			}
			$em->remove($building);
			$em->flush();
		}
		
		return $this->redirect($this->generateUrl('adminBuilding'));   
	}

	public function editAction($idBuilding)
	{
		$em=$this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);       
        $form    = $this->createForm(new BuildingType(), $building);
				
		return $this->render('OptimusOptimusBundle:Building:edit.html.twig', array(
            'entity' => $building,
            'form'   => $form->createView(),
			'idBuilding' =>$idBuilding
        ));
	}
	
	public function saveAction(Request $request, $idBuilding)
	{
		$em=$this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);       
        $form    = $this->createForm(new BuildingType(), $building);
		
		$form->handleRequest($request);

        if ($form->isValid()) {            
            $em->persist($building);
            $em->flush();
			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Edit Building", "Building", $idBuilding, 1, $ip, $idBuilding, "edit" );
			
			return $this->redirect($this->generateUrl('adminBuilding'));           
        }else{
			return false;
		}
	}

	//Get description of building
	public function descriptionAction($idBuilding)
	{
		$em = $this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		
		return $this->render('OptimusOptimusBundle:Building:description.html.twig', array("idBuilding"=>$idBuilding, "building"=>$building));
	}
	
	//Get Real time indicators of building
	public function globalConfigBuildingAction($idBuilding)
	{
		$em = $this->getDoctrine()->getManager();
		
		$buildingSRTime=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building"=> $idBuilding));		
		$sensorsRTime=$this->get('service_sensorsRTime')->getDataVariablesInput();
		
		//Form
		$entity = new BuildingSensorsRTime();
        $form   = $this->createForm(new BuildingSensorsRTimeType(array('attr' => array('idBuilding' => $idBuilding))), $entity);
		
		return $this->render('OptimusOptimusBundle:Admin:adminGlobalBuilding.html.twig', array(
			"buildingSRTime"=>$buildingSRTime, 
			"sensorsRTime"=>$sensorsRTime, 
			"form"=> $form->createView(), 
			"idBuilding"=>$idBuilding
		));
	}
	
	public function addSensorRTimeAction(Request $request, $idBuilding)
	{
		$em = $this->getDoctrine()->getManager();
		
		$sensorMapping  = new BuildingSensorsRTime();       
        $form    = $this->createForm(new BuildingSensorsRTimeType(array('attr' => array('idBuilding' => $idBuilding))), $sensorMapping);
		$form->handleRequest($request);
		
		//$building=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->find($request->request->get('idPartition'));
		
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		
        if ($form->isValid()) 
		{				
			$sensorMapping->setFkBuilding($building);
            $em->persist($sensorMapping);
            $em->flush();
			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			//$this->get('service_event')->createEvent($user, "New sensor", "Mapping", $idSensor, 1, $ip, $idBuilding, "create" );
			
			return $this->redirect($this->generateUrl('globalConfigBuilding', array('idBuilding' => $idBuilding)));
		}
		return new Response("ok");
	}
	
	public function editSensorRTimeAction($idBuilding, $idSensorRTime)
	{
		$em=$this->getDoctrine()->getManager();
		$sensorMapping=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->find($idSensorRTime);       
        $form = $this->createForm(new BuildingSensorsRTimeType(array('attr' => array('idBuilding' => $idBuilding))), $sensorMapping);
				
		return $this->render('OptimusOptimusBundle:Building:editSensorRTime.html.twig', array(
            'entity' => $sensorMapping,
            'form'   => $form->createView(),
			'idBuilding' =>$idBuilding,
			'idSensorRTime' =>$idSensorRTime,
        ));	
	}
	
	public function saveSensorRTimeAction(Request $request, $idBuilding, $idSensorRTime)
	{
		$em=$this->getDoctrine()->getManager();
		$sensorMapping=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->find($idSensorRTime);       
        $form    = $this->createForm(new BuildingSensorsRTimeType(array('attr' => array('idBuilding' => $idBuilding))), $sensorMapping);
		
		$form->handleRequest($request);

        if ($form->isValid()) 
		{   			
			//create event
			/*$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "edit sensor from mapping", "Mapping", $idSensorPartition, 1, $ip, $idBuilding, "edit" );*/
			
            $em->persist($sensorMapping);
            $em->flush();
			
			return $this->redirect($this->generateUrl('globalConfigBuilding', array('idBuilding' => $idBuilding)));           
        }else{
			return false;
		}
	}
	
	public function deleteSensorRTimeAction($idBuilding, $idSensorRTime)
	{
		$em = $this->getDoctrine()->getManager();
		
		$sensorMapping=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->find($idSensorRTime);
		
		if($sensorMapping)
		{
			//create event
			/*$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Delete sensor from mapping", "Mapping", $idSensorPartition, 1, $ip, $idBuilding, "delete" );*/
			
			//Cuando se elimina sus hijos tb se eliminan
			$em->remove($sensorMapping);
			$em->flush();			
		}
		
		return $this->redirect($this->generateUrl('globalConfigBuilding', array('idBuilding' => $idBuilding)));
	}
	
	
}