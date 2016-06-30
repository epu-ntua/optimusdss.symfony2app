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
		if($from=='')
		{			
			$dateActual=new \DateTime();			
			
			$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d")." 00:00:00";
			$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d H:i:s");
			
			$startDateView = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d");		
			$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d");
			
			
		}else{
			$startDateFunction=$from." 00:00:00";
			$startDateView=$from;
			$dateActual=new \DateTime();			
			
			$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $from." 00:00:00")->format("Y-m-d H:i:s");
			$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $to." 00:00:00")->format("Y-m-d H:i:s");
		}
		
		if($to!='')
		{
			$endDate=$to;
		}
		
		//Get data
		set_time_limit(0);
		$data['dataFinal']=$this->get('service_data_capturing')->getDataFromDate($dTo, $dFrom,'','','variable',$idBuilding);	
		//$data['dataFinal'] = array();
		
		
		$data['dataRT']=$this->get('service_sensorsRTime')->getRTTime($dTo, $dFrom, '', $idBuilding);
		//dump($data['dataRT']);
		
		
		//Get data for the compound chart
		$data['mappingVariable'] = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $dFrom, $idBuilding);
		//$data['mappingVariable'] = array();
		
		//dump($data['mappingVariable']);
		//dates to view
		$data['startDate']=$startDateView;
		$data['endDate']=$endDate;
		$actualDate=new \DateTime();
		$data['actualDate']=$actualDate->format("Y-m-d");
		
		//id building
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		$data['lastDateForecasted']=$this->get('service_data_capturing')->lastForecastedDate($idBuilding);
				
		//$price = $this->get('service_pricePredictor')->getPricePredictor('2015-09-13 00:00:00', '2015-10-13 00:00:00', 'electricity_hourly_prices', 'variable', $idBuilding);
			
		//dump($price);

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