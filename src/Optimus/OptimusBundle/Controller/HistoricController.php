<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HistoricController extends Controller
{
	
	public function indexAction($from='', $to='', $timeSelected='')
    {	
		//$container = new ContainerBuilder();
		//$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
		//$loader->load('C:\xampp\htdocs\Symfony\app\config\config_custom.yml');		
			
		//$this->container->get('logger')->info('Mi mensaje'); 
			
		$data['allData']['allSensors']=$this->processData($from, $to, $timeSelected);
		
		//dump($data['allData']['allSensors']);
			
		if($timeSelected=='')	$data['allData']['timeSelected']='week';
		else $data['allData']['timeSelected']=$timeSelected;
		
		if($from!='') 	$data['allData']['startDate']=$from;
		else			$data['allData']['startDate']="2014-11-23";
		if($to!='') 	$data['allData']['endDate']=$to;
		else 			$data['allData']['endDate']="2014-11-29";
		   
		return $this->render('OptimusOptimusBundle:Graph:main_3.html.twig', $data);
		//return $this->render('OptimusOptimusBundle:Layout:Layout.html.twig', $data);
    }
	
	private function processData($from='', $to='', $timeSelected='')
	{
		$aAllSensors=$aColors=array();
		$numSensors=$this->container->getParameter('OPTIMUS_SENSORS');
		
		for($i=1; $i<=$numSensors; $i++)
		{
			$aDataSensor=array();
			$sensorPre=$this->container->getParameter("OPTIMUS_SENSOR_".$i);
			//$this->container->get('logger')->info($sensorPre);			
			
			if($sensorPre['display']=='yes')
			{
				$this->container->get('logger')->info("yes");
				if($timeSelected=='day') {
					$fromFinal=$from.' 00:00:00';
					$toFinal=$to.' 23:59:00';					
				}else{
					$fromFinal=$from;
					$toFinal=$to;
				}
				$gestorOntologia = $this->get('service_ontologia');
				$aDataSensor=$gestorOntologia->getDataParameterFromOntology($fromFinal, $toFinal, $sensorPre['url']);
				
				$nameComplet=explode("sensingdevice/", $sensorPre['url']);
				$name=explode("_",$nameComplet[1]);
				
				if(array_search($name[1], $aColors)===false){
					$aColors[$name[1]]="#".dechex(rand(0x000000, 0xFFFFFF));
					$type="lines";
				}else $type="points";
				
				//$aDataSensor[$i]['value']=4;
				//$aAllSensors[]=array("name"=>$name[1], "values"=>$aDataSensor, "color"=>$aColors[$name[1]], "type"=>$type); 
				$aAllSensors[]=array("name"=>$sensorPre['name'], "values"=>$aDataSensor, "color"=>$sensorPre['color'], "type"=>$type); //$aColors[$name[1]] //$aDataSensor
			}
		}
		
		$dateActual=new \DateTime();
		$aAllVariablesHistoricals=$this->get('service_data_capturing')->getDataFromDate($dateActual->format("Y-m-d H:i:s"), '2015-04-05 00:00:00','','variable', 'historic');
		
		return $aAllSensors;
	}
	
	function getDate($dateCalcula)
	{
		return date("F j, Y H:i:s", strtotime($dateCalcula));
	}
}


