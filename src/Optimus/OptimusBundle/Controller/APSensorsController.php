<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Optimus\OptimusBundle\Entity\APSensors;
use Optimus\OptimusBundle\Form\APSensorsType;

class APSensorsController extends Controller
{			
    public function mappingAction($idBuilding, $idActionPlan)
    {
		//get partitions
		$em = $this->getDoctrine()->getManager();
		$partitions=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		
		$str='';
		$aMappings=array();
		//para cada partition --> get sensors para la partition y el action plan actuales
		if($partitions)
		{
			$str='[';
			$i=0;
			
			foreach($partitions as $partition)
			{				
				if($partition->getFkBuildingPartitioning()==null)
					$str.="{ 'id':'".$partition->getId()."', 'parent':'#', 'text':'".$partition->getPartitionName()."','children' : false }";
				else
					$str.=",{ 'id':'".$partition->getId()."', 'parent':'".$partition->getFkBuildingPartitioning()->getId()."', 'text':'".$partition->getPartitionName()."' }";
					
				$sensors=$em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_BuildingPartitioning"=>$partition->getId(), "fk_actionplan"=>$idActionPlan));
				
				$aMappings[]=array("partition"=>$partition, "sensorsPartition"=>$sensors);
			}
			$str.=']';
		}
		
		//data Action plan
		$dataActionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->find($idActionPlan);
		//tree
		$tree=str_replace("'","\"",$str);
		//Form
		$entity = new APSensors();
        $form   = $this->createForm(new APSensorsType(array('attr' => array('idBuilding' => $idBuilding))), $entity);
			
		$aVariablesInput=$this->getNamesVariablesInputs($dataActionPlan->getType(), $idBuilding);
		
		$nameBuilding=$this->get('service_data_capturing')->getNameBuilding($idBuilding);

		return $this->render('OptimusOptimusBundle:APSensors:mapping.html.twig', array("aMappings"=>$aMappings, "strTree"=>$tree, "idBuilding" => $idBuilding, "idActionPlan"=>$idActionPlan, 'form'   => $form->createView(), "typeActionPlan"=>$dataActionPlan->getType(), "nameActionPlan"=>$dataActionPlan->getName(), "aVariablesInput"=>$aVariablesInput, "nameBuilding"=>$nameBuilding));		
    }
	
	private function getNamesVariablesInputs($typeActionPlan, $idBuilding)
	{
		$aVariablesInput=array();
		
		switch ($typeActionPlan)
		{	
			case 1:
				$aVariablesInput=$this->get('service_apoc')->getDataVariablesInput();
				break;
			case 2:
				$aVariablesInput=$this->get('service_apspmc')->getDataVariablesInput();
				break;
			case 4:
				$aVariablesInput=$this->get('service_appreheating')->getDataVariablesInput();
				break;
			case 5:
				$aVariablesInput=$this->get('service_appvm')->getDataVariablesInput($this->get('service_data_capturing')->getNameBuilding($idBuilding));
				break;
			case 6:
				$aVariablesInput=$this->get('service_appv')->getDataVariablesInput();
				break;
			case 7:
				$aVariablesInput=$this->get('service_apsource')->getDataVariablesInput();
				break;
			case 8:
				$aVariablesInput=$this->get('service_apeconomizer')->getDataVariablesInput();
				break;
		}
		
		return $aVariablesInput;
	}
	
	public function addSensorPartitionAction(Request $request, $idBuilding, $idActionPlan)
	{
		$em = $this->getDoctrine()->getManager();
		
		$apsensors  = new APSensors();       
        $form    = $this->createForm(new APSensorsType(array('attr' => array('idBuilding' => $idBuilding))), $apsensors);
		$form->handleRequest($request);
		
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->find($idActionPlan);
		//dump($actionPlan);
		$building=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->find($request->request->get('idPartition'));
		
        if ($form->isValid()) 
		{	
			//var_dump($apsensors); 	
			$order=(int)$request->request->get("order");
			
			$apsensors->setFkActionplan($actionPlan);
			$apsensors->setFkBuildingPartitioning($building);
			$apsensors->setOrderSensor($order);
			
			$em->persist($apsensors);
            $em->flush();
			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			//$this->get('service_event')->createEvent($user, "New sensor", "Mapping", $idSensor, 1, $ip, $idBuilding, "create" );
			
			return $this->redirect($this->generateUrl('actionPlan_mapping', array('idBuilding' => $idBuilding, 'idActionPlan'=>$idActionPlan)));
		}
		return new Response("ok");
	}
	
	public function deleteSensorPartitionAction($idBuilding, $idActionPlan, $idSensorPartition, $orderSensor)
	{
		$em = $this->getDoctrine()->getManager();
		
		$apsensors=$em->getRepository('OptimusOptimusBundle:APSensors')->find($idSensorPartition);
		
		if($apsensors)
		{
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Delete sensor from mapping", "Mapping", $idSensorPartition, 1, $ip, $idBuilding, "delete" );
			
			//Cuando se elimina sus hijos tb se eliminan
			$em->remove($apsensors);
			$em->flush();			
		}
		
		return $this->redirect($this->generateUrl('actionPlan_mapping', array('idBuilding' => $idBuilding, 'idActionPlan'=>$idActionPlan)));
	}
	
	public function editSensorPartitionAction($idBuilding, $idActionPlan, $idSensorPartition, $orderSensor)
	{
		$em=$this->getDoctrine()->getManager();
		$sensorpartition=$em->getRepository('OptimusOptimusBundle:APSensors')->find($idSensorPartition);       
        $form = $this->createForm(new APSensorsType(array('attr' => array('idBuilding' => $idBuilding))), $sensorpartition);

		return $this->render('OptimusOptimusBundle:APSensors:edit.html.twig', array(
            'entity' => $sensorpartition,
            'form'   => $form->createView(),
			'idBuilding' =>$idBuilding,
			'idActionPlan' =>$idActionPlan,
			'idSensorPartition' =>$idSensorPartition,
			'orderSensor' =>$orderSensor
        ));	
	}
	
	public function saveSensorPartitionAction(Request $request, $idBuilding, $idActionPlan, $idSensorPartition, $orderSensor)
	{
		$em=$this->getDoctrine()->getManager();
		$sensorpartition=$em->getRepository('OptimusOptimusBundle:APSensors')->find($idSensorPartition);       
        $form    = $this->createForm(new APSensorsType(array('attr' => array('idBuilding' => $idBuilding))), $sensorpartition);
		
		$form->handleRequest($request);

        if ($form->isValid()) 
		{   			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "edit sensor from mapping", "Mapping", $idSensorPartition, 1, $ip, $idBuilding, "edit" );
			
            $em->persist($sensorpartition);
            $em->flush();
			
			return $this->redirect($this->generateUrl('actionPlan_mapping', array('idBuilding' => $idBuilding, 'idActionPlan'=>$idActionPlan)));           
        }else{
			return false;
		}
	}
		
}