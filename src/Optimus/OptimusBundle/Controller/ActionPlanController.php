<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class ActionPlanController extends Controller
{
			
    public function indexAction($from='', $to='', $timeSelected='')
    {	
		//Seleccionar el Action Plan correspondiente
				
		/*if($from!='') 	$data['startDate']=$from;
		//else			$data['allData']['startDate']="2014-11-23";
		//else			$data['allData']['startDate']="2015-03-11";
		else			$data['startDate']="2015-04-12";
		if($to!='') 	$data['endDate']=$to;
		//else 			$data['allData']['endDate']="2014-11-29";
		//else 			$data['allData']['endDate']="2015-03-17";
		else 			$data['endDate']="2015-04-18";*/
		
		
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
		

		
		//$dataActionPlan = $this->get('gestor_data_capturing')->getDataFromDate("2015-04-15 00:00:00", $data['allData']['startDate']." 00:00:00", "Energy production");
		$idActionPlan=1;
		//$data['dataActionPlan'] = $this->get('gestor_data_capturing')->getDataPVActionPlan("2015-04-12 00:00:00", $idActionPlan);
		$data['dataActionPlan'] = $this->get('gestor_data_capturing')->getDataPVActionPlan($startDateFunction, $idActionPlan);
		
		
		return $this->render('OptimusOptimusBundle:Graph:actionPlan.html.twig', $data);
		//return $this->redirect($this->generateUrl('prediction'));
    }
	
	public function newCalculateAction($from='', $to='')
	{
		$this->get('gestor_calculo')->createPredictionAndCalculates($from);		
		return $this->indexAction($from, $to);
	}
	
			
}