<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\Sensor;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class PredictionController extends Controller
{			
    public function indexAction($idBuilding, $from='', $to='')
    {		
		//Dates			
		$dateActual=new \DateTime();
		if($from=='')
		{		
			if($to==''){
				$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->modify("midnight")->format("Y-m-d H:i:s");
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d")." 23:59:59")->modify("-1 day")->format("Y-m-d H:i:s");	
				
				$startDateView = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->modify("midnight")->format("Y-m-d");	
				$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d")." 23:59:59")->modify("-1 day")->format("Y-m-d H:i:s");	
			}else{
				$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d")." 00:00:00";
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $to." 23:59:59")->format("Y-m-d")." 23:59:59";
				
				$startDateView = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d")." 00:00:00";	
				$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $to." 23:59:59")->format("Y-m-d");
			}
			
		}else
		{	
			if($to==''){
				$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $from." 00:00:00")->modify("-7 day")->modify("midnight")->format("Y-m-d H:i:s");
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("midnight")->format("Y-m-d H:i:s");	
				
				$startDateView=$from;	
				$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d");
			}else{  
				$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $from." 00:00:00")->format("Y-m-d H:i:s");
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $to." 23:59:59")->format("Y-m-d H:i:s");
				
				$startDateView=$from;	
				$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $to." 23:59:59")->format("Y-m-d H:i:s");	
			}
			
		}
		

		//Get data
		set_time_limit(0);
		$data['dataFinal']=$this->get('service_data_capturing')->getDataFromDate($dateActual->format("Y-m-d H:i:s"), $dFrom, $dTo,'','variable',$idBuilding);	
		//dump($data['dataFinal'][59]);
		for ($i = 0; $i < count($data['dataFinal']); $i++){
			$lastIndex = count($data['dataFinal'][$i]['values']) - 1;
			if($lastIndex != -1){
				$lastDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data['dataFinal'][$i]['values'][$lastIndex]['date']);
				$toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dTo);
				$diff = $toDate->diff($lastDate);
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				for ($nextHour = 1; $nextHour < $hours+1; $nextHour++){
					$data['dataFinal'][$i]['values'][] = ["date"=> $lastDate->modify("+1 hours")->format('Y-m-d H:i:s'), "value" => '0'];
				}
			}
		}
		
		
		$data['dataRT']=$this->get('service_sensorsRTime')->getRTTime($dTo, $dFrom, '', $idBuilding);		
		
		//Get data for the compound chart
		$data['mappingVariable'] = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $dFrom, $idBuilding);
		//dump($data['mappingVariable']);
		for ($i = 0; $i < count($data['mappingVariable']); $i++){
			$lastIndex = count($data['mappingVariable'][$i]['data']) - 1;
			if($lastIndex != -1){
				$lastDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data['mappingVariable'][$i]['data'][$lastIndex]['date']);
				$toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dTo);
				$diff = $toDate->diff($lastDate);
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				for ($nextHour = 1; $nextHour < $hours; $nextHour++){
					$data['mappingVariable'][$i]['data'][] = ["date"=> $lastDate->modify("+1 hours")->format('Y-m-d H:i:s'), "value" => '0'];
				}
			}
		}
		
		//dates to view
		$data['startDate']=$startDateView;
		$data['endDate']=$endDate;
		$actualDate=new \DateTime();
		$data['actualDate']=$actualDate->format("Y-m-d H:i:s");
		
		//id building
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		$data['lastDateForecasted']=$this->get('service_data_capturing')->lastForecastedDate($idBuilding);
				
		//$price = $this->get('service_pricePredictor')->getPricePredictor('2015-09-13 00:00:00', '2015-10-13 00:00:00', 'electricity_hourly_prices', 'variable', $idBuilding);
			
		return $this->render('OptimusOptimusBundle:Prediction:graphPredictions.html.twig', $data);
    }
	
	function newPredictionAction($idBuilding, $from='', $to='')
	{	
		//event
		$em = $this->getDoctrine()->getManager();
		$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
		$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
		
		//New calculation
		set_time_limit(0);
		$this->get('service_calculo')->createPredictionAndCalculates($from, $idBuilding, $ip, $user);
		
		return $this->indexAction($idBuilding, $from, $to);
	}	
}