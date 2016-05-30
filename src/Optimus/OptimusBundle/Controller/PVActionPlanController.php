<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PVActionPlanController extends Controller
{			
    public function indexAction($idBuilding, $idAPType='', $from='', $to='', $timeSelected='')
    {	
		//dump($idAPType);
	
	
		if($from=='')
		{
			$dateActual=new \DateTime();
			$startDate=$dateActual->modify("+1 day");			
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
		
		//events
		//$this->get('service_event')->lastsEvent($idBuilding);
		
		// 1. Get info from the Action Plan (DB):
		$em = $this->getDoctrine()->getManager();
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
			
			$data['dataActionPlan'] = $this->get('service_appv_presenter')->getDataValues($startDateFunction, $idActionPlan);
			$data['weekSoldProducedAcumulated'] = number_format($this->get('service_data_capturing')->weekSoldProducedAcumulated, 2, ',', '.');
			$data['weekEmissionsAcumulated'] = number_format($this->get('service_data_capturing')->weekEmissionsAcumulated, 2, ',', ' ');
			
			$colorsAP=$this->get('service_appv')->getColorsVariables();
			$data['unitsAP']=$this->get('service_appv')->getUnitsVariables();
			
			$data['dataPartitions'] =$this->get('service_data_capturing')->getDataChartTable($idBuilding, $idActionPlan, $startDateFunction, $colorsAP);
		}
		
		//dump($data);
				
		return $this->render('OptimusOptimusBundle:PVActionPlan:PVActionPlan.html.twig', $data);		
    }
	
	// This function is called from a javascript function (registered on routing.yml):
	public function newCalculatePVAction($idBuilding, $idAPType, $from='', $to='')
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
		$outputDay=$em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findBy(array("id"=>$idOutputDay));
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
			
			$textEvent=" sale/consumption of the electricity produced through the PV system ";
			$context="Action plan - scheduling of the sale/consumption of the electricity produced through the PV system";
			$visible=1;
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$idActionPlan=(int)$elementsForm['idActionPlan'];
			
			$this->get('service_event')->createEvent($user, $textEvent, $context, $idActionPlan, 1, $ip, $request->request->get('idBuilding'), $newStatus);
		}
		
		return new Response("ok");
	}
	
	public function changeStrategyAction(Request $request)
	{
		$idOutputDay=$request->request->get('idOutputDay');
		$type=$request->request->get('type');
		
		$em = $this->getDoctrine()->getManager();		
		$outputDay=$em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findBy(array("id"=>$idOutputDay));
		if($outputDay)
		{
			foreach($outputDay as $output)
			{
				$output->setStrategy($type);
				$em->flush();
				
				
			}
		}	
				
		return new Response("ok");
	}
	
	public function changeStrategyWeekAction(Request $request)
	{
		try
		{
			$idListOutputDay=$request->request->get('idListOutputDay'); // List of ID's for a week (Monday-Sunday)
			$type=$request->request->get('type');						
			
			if($idListOutputDay != null)
			{
				$em = $this->getDoctrine()->getManager();
				
				foreach($idListOutputDay as $idOutputDay)
				{
					$outputDay=$em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findBy(array("id"=>$idOutputDay));
					if($outputDay)
					{
						foreach($outputDay as $output)
						{
							$output->setStrategy($type);
							$em->flush();
							
							//create event status 				
							$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
								
							$textEvent=" has changed strategy to ".$type;
							$context="Action Plan - scheduling of the sale/consumption of the electricity produced through the PV system";
							$visible=1;
							$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();				
							
							$this->get('service_event')->createEvent($user, $textEvent, $context, $request->request->get('idActionPlan'), 1, $ip, $request->request->get('idBuilding'), "update" );
							
						}
					}	
				}
			}
		}
		catch(Exception $e)
		{
			$response = new Response();
			$response->setContent(json_encode(array('message' => $e->getMessage())));
			$response->setStatusCode(419);
			return $response;
		}
		
		return new Response("ok");
	}		
}