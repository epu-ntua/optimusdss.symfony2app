<?php


namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Optimus\OptimusBundle\Servicios\ServiceAPEconomizer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EconomizerActionPlanController extends Controller
{
    public function indexAction($idBuilding, $idAPType='', $from='', $to='', $method='')
    {
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
		
		if($method=='') $method = "temp";
		
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
		    
			$data['dataActionPlan'] = $this->get('service_apeconomizer_presenter')->getDataValues($idActionPlan, $idBuilding, $startDateFunction, $to, $method);			
		}
		
        // render the template
        return $this->render('OptimusOptimusBundle:EconomizerActionPlan:economizerActionPlan.html.twig', $data);

    }
	
	
	
	public function changeInternalDayAction(Request $request)
	{		
		$em = $this->getDoctrine()->getManager();	
		$idOutputDay = (string)$request->request->get('idOutputDay');
		$idOutputDay = explode(" ", $idOutputDay);
		
		$temp_internal = (string)$request->request->get('temp_internal');
		$temp_internal = explode(" ", $temp_internal);
		
		$enth_internal = (string)$request->request->get('enth_internal');
		$enth_internal = explode(" ", $enth_internal);
		
		$success = true;
		for($i=0; $i<count($idOutputDay); $i++){
			$outputDay=$em->getRepository('OptimusOptimusBundle:APEconomizerOutputDay')->findBy(array("id"=>(int)$idOutputDay[$i]));
			if($outputDay)
			{	
				$outputDay[0]->setTemp_internal((float)$temp_internal[$i]);
				$outputDay[0]->setEnth_internal((float)$enth_internal[$i]);
				$em->flush();
			}
			else{
				$success = false;
			}
		}
		
		if($success)
			return new Response("ok");
		else
			return new Response("error");
		
	}
	
	
	// This function is called from a javascript function (registered on routing.yml):
	public function newCalculateEconomizerAction($idBuilding, $idAPType, $from='', $to='')
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
		$outputDay=$em->getRepository('OptimusOptimusBundle:APEconomizerOutputDay')->findBy(array("id"=>$idOutputDay));
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
			
			$textEvent=" Economizer plan ";
			$context="Action plan -  Air side economizer";
			$visible=1;
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$idActionPlan=(int)$elementsForm['idActionPlan'];
			
			$this->get('service_event')->createEvent($user, $textEvent, $context, $idActionPlan, 1, $ip, $request->request->get('idBuilding'), $newStatus);
		}
		
		return new Response("ok");
	}
}