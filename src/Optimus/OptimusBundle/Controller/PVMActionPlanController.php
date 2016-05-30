<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PVMActionPlanController extends Controller
{
	public function indexAction($idBuilding, $idAPType='', $from='', $to='', $timeSelected='')
    {
		//dump($idAPType);
		
		
		if($from=='')
		{
			$dateActual=new \DateTime();
			$startDate=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("last monday");				
			$startDateFunction=$startDate->format("Y-m-d H:i:s");
			$data['startDate']=$startDate->format("Y-m-d");
			$data['endDate']=\DateTime::createFromFormat('Y-m-d', $startDate->format("Y-m-d"))->modify("+6 day")->format("Y-m-d");
		}else{
			$startDateFunction=$from." 00:00:00";
			$data['startDate']=$from;
			$data['endDate']=$to;
		}	
	
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		$data['idAPType']=$idAPType;
	
		// 1. Get info from the Action Plan (DB):
		$em=$this->getDoctrine()->getManager();		
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>$idAPType));		
		//dump($actionPlan);
		
		if($actionPlan != null)
		{
			// 2. Get info from data capturing module (config file -> service):
			$idActionPlan=$actionPlan[0]->getId();
			$data['idActionPlan']=$idActionPlan;
			$data['dataActionPlan_name'] = $actionPlan[0]->getName();
			$data['dataActionPlan_description'] = $actionPlan[0]->getDescription();
			$data['dataActionPlan_lastCalculation']=$this->get('service_data_capturing')->getLastCalculated($idActionPlan);
			
			$data['dataActionPlan'] = $this->get('service_appvm_presenter')->getDataValues($idActionPlan, $idBuilding, $startDateFunction, $to);
			
			$colorsAP=$this->get('service_appvm')->getColorsVariables();
			
			$data['dataPartitions'] =$this->get('service_data_capturing')->getDataChartTable($idBuilding, $idActionPlan, $startDateFunction, $colorsAP);
		}
		
		//dump($data);
	
		return $this->render('OptimusOptimusBundle:PVMActionPlan:PVMActionPlan.html.twig', $data);
	}
	
	// This function is called from a javascript function (registered on routing.yml):
	public function newCalculatePVMAction($idBuilding, $idAPType, $from='', $to='')
	{
		//dump($idAPType);
		
		//event
		$em = $this->getDoctrine()->getManager();
		$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
		$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
	
		$this->get('service_calculo')->createPredictionAndCalculates($from, $idBuilding, $ip, $user); // <--- !!!!!!! TEMP
		return $this->indexAction($idBuilding, $idAPType, $from, $to);
	}
	
	// Cambia el estado de un dia en el action plan 
	public function changeStatusDayAction(Request $request, $idOutputDay)
	{		
		$em = $this->getDoctrine()->getManager();		
		$outputDay=$em->getRepository('OptimusOptimusBundle:APPVMOutputDay')->findBy(array("id"=>$idOutputDay));
		$elementsForm=array();
		
		if($outputDay)
		{			
			parse_str($request->request->get('data'), $elementsForm);		
			$status=(int)$elementsForm['filter'];
			$outputDay[0]->setStatus($status);
			$em->flush();
			
			//create event status <--- !!!!!!! TEMP		
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			
			if($status==0) 			$newStatus="unknowns";
			elseif($status==1) 		$newStatus="accepts";
			else 					$newStatus="declines";
			
			$textEvent=" Scheduling the PV maintenance ";
			$context="Action plan -  Scheduling the PV maintenance";
			$visible=1;
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$idActionPlan=(int)$elementsForm['idActionPlan'];
			
			$this->get('service_event')->createEvent($user, $textEvent, $context, $idActionPlan, 1, $ip, $request->request->get('idBuilding'), $newStatus);
		}
		
		return new Response("ok");
	}
		
}