<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

use Optimus\OptimusBundle\Entity\Sensor;
use Optimus\OptimusBundle\Form\SensorType;

class SensorController extends Controller
{
			
    public function indexAction()
    {			
		$numSensors=$this->container->getParameter('OPTIMUS_SENSORS');
		
		for($i=1; $i<=$numSensors; $i++)
		{			
			$sensorPre=$this->container->getParameter("OPTIMUS_SENSOR_".$i);
						
			//buscar si existe en la base datos
			$nameSensor = $this->getDoctrine()->getRepository('OptimusOptimusBundle:Sensor')->findOneByName($sensorPre['name']);
			
			//$em = $this->getDoctrine()->getManager();
			//$user = $em->getRepository('SouzoundSZBundle:Usuario')->findOneByNickname($nickname);
			
			if (!$nameSensor)
			{
				//si no existe insertarlo
				$sensor = new Sensor();
				$sensor->setName($sensorPre['name']);
				$sensor->setUrl($sensorPre['url']);
				$sensor->setColor($sensorPre['color']);
				$sensor->setDisplay($sensorPre['display']);
			 
				$em = $this->getDoctrine()->getManager();
				$em->persist($sensor);
				$em->flush();
			}			
			
		}
		//dump($this->generateUrl('prediction'));
		//return $this->render('OptimusOptimusBundle:Layouts:Layout.html.twig');
		return $this->redirect($this->generateUrl('prediction'));
    }
	
	public function getAdminSensorsAction($idBuilding)
	{
		$em=$this->getDoctrine()->getManager();
		$sensors=$em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array("fk_Building"=>$idBuilding));
					
		//dump($sensors);
		$nameBuilding=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		return $this->render('OptimusOptimusBundle:Admin:adminSensors.html.twig', array("idBuilding"=>$idBuilding, "sensors"=>$sensors, "nameBuilding"=>$nameBuilding));
	}
	
	public function checkStatusSensorsAction($idBuilding)
	{
		set_time_limit(0);
		ini_set('memory_limit','64M');
		$this->get('service_sensorStatus')->checkStatus();
		
		return $this->redirect($this->generateUrl("adminSensors", array('idBuilding' => $idBuilding)));  
	}
	
	public function newAction($idBuilding)
	{	
		$entity = new Sensor();
        $form   = $this->createForm(new SensorType(), $entity);
		
		//dump($idBuilding);
		
        return $this->render('OptimusOptimusBundle:Sensor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
			'idBuilding' =>$idBuilding
        ));
	}
	
	public function createAction(Request $request, $idBuilding)
	{
		$sensor  = new Sensor();       
        $form    = $this->createForm(new SensorType(), $sensor);
		$form->handleRequest($request);
		
        if ($form->isValid()) {           
            $em = $this->getDoctrine()->getManager();
			$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
			$sensor->setFkBuilding($building);
			$sensor->setStatus("-");
			$sensor->setLastData("-");
            $em->persist($sensor);
            $em->flush();
			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Create sensor", "Sensor", $sensor->getId(), 1, $ip, $idBuilding, "create" );
			
			return $this->redirect($this->generateUrl("adminSensors", array('idBuilding' => $idBuilding)));            
        }else{
			return false;
		}

        /*return $this->render('OptimusOptimusBundle:Building:new.html.twig', array(
            'entity' => $building,
            'form'   => $form->createView()
        ));*/
		//return ($this->newAction());
	}
	
	public function editAction($idBuilding, $idSensor)
	{
		
		$em=$this->getDoctrine()->getManager();
		$sensor=$em->getRepository('OptimusOptimusBundle:Sensor')->find($idSensor);       
        $form    = $this->createForm(new SensorType(), $sensor);
		
		set_time_limit(0);
		ini_set('memory_limit', '128M');
		return $this->render('OptimusOptimusBundle:Sensor:edit.html.twig', array(
            'entity' => $sensor,
            'form'   => $form->createView(),
			'idBuilding' =>$idBuilding,
			'idSensor' =>$idSensor
        ));		
	}
	
	public function saveAction(Request $request, $idBuilding, $idSensor)
	{
		$em=$this->getDoctrine()->getManager();
		$sensor=$em->getRepository('OptimusOptimusBundle:Sensor')->find($idSensor);       
        $form    = $this->createForm(new SensorType(), $sensor);
		
		$form->handleRequest($request);

        if ($form->isValid()) {            
            $em->persist($sensor);
            $em->flush();
			
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Edit sensor", "Sensor", $idSensor, 1, $ip, $idBuilding, "edit" );
			
			return $this->redirect($this->generateUrl("adminSensors", array('idBuilding' => $idBuilding)));           
        }else{
			return false;
		}
		
	}
	
	public function deleteAction($idBuilding, $idSensor)
	{
		$em=$this->getDoctrine()->getManager();
		$sensor=$em->getRepository('OptimusOptimusBundle:Sensor')->find($idSensor);
		
		if($sensor)
		{
			//create event
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Delete sensor", "Sensor", $idSensor, 1, $ip, $idBuilding, "delete" );
			
			
			$em->remove($sensor);
			$em->flush();
		}
		
		return $this->redirect($this->generateUrl("adminSensors", array('idBuilding' => $idBuilding)));
	}
	
	
}