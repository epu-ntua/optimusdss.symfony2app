<?php


namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Optimus\OptimusBundle\Servicios\ServiceAPSource;

class EnergySourceActionPlanController extends Controller
{
    public function plan_indexAction($idBuilding, $idAPType='', $from='', $to='')
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
			
		    $data['dataActionPlan'] = $this->get('service_apsource_presenter')->getDataValues($idActionPlan, $idBuilding, $startDateFunction, $to);			
		    //$data['dataActionPlan'] = $this->get('service_apsource')->invoke_service($idBuilding);
		}
		

        // render the template
        return $this->render('OptimusOptimusBundle:EnergySourceActionPlan:energySourceActionPlan.html.twig', $data);

    }
	
		// This function is called from a javascript function (registered on routing.yml):
	public function newCalculateESAction($idBuilding, $idAPType, $from='', $to='')
	{
		//dump($idAPType);
		
		//event
		$em = $this->getDoctrine()->getManager();
		$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
		$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
	
		$this->get('service_calculo')->createPredictionAndCalculates($from, $idBuilding, $ip, $user); // <--- !!!!!!! TEMP
		return $this->plan_indexAction($idBuilding, $idAPType, $from, $to);
	}
}