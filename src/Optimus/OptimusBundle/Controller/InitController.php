<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class InitController extends Controller
{
	//List Buildings --- NEW 
	public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
		$buildings=$em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		$datesBuildings=$this->lastsDatesBuildings($buildings);
		
		//Dates
		$dateActual=new \DateTime();
		$thisMonday=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d")." 00:00:00";
		$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d H:i:s");
		$startDate=$thisMonday;
		$endDate=$dateActual;
		
		//dump($buildings);
		
		$dataBuildings=array();
		foreach ($buildings as $building)
		{
			//Data average RTime, User Activity & Chart Activity
			//$dataBuildings[$building->getId()]['dataDashboard']=$this->getDataDashboard($building);
			$aBuilding = array();
			$aBuilding[]=$building;
			$dataBuildings[$building->getId()]['globalRTime']=$this->globalValuesRTime($aBuilding);		
		}
		
		$unitsRTime=$this->get('service_sensorsRTime')->getUnitsVariables();
		$colorsRTime = $this->get('service_sensorsRTime')->getColorsVariables();
		
		//dump($dataBuildings);
		
		return $this->render('OptimusOptimusBundle:Building:listBuildings.html.twig', array('buildings'=>$buildings, "datesBuildings"=>$datesBuildings, "startDate"=>$startDate, "endDate"=>$endDate, "unitsRTime"=>$unitsRTime, "dataBuildings"=>$dataBuildings, "colorsRTime"=>$colorsRTime));
		
	}
	
	//Dashboard of city with graph RTime --- NEW 
	public function cityDashboardAction()
	{
		$em = $this->getDoctrine()->getManager();
		$buildings=$em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		//Data to Charts RTime & User Activity
		$datesBuildings=$this->lastsDatesBuildings($buildings);
		$globalRTime=$this->globalValuesRTime($buildings);
		$dataDashboard=$this->getDataDashboard($buildings);
		$unitsRTime=$this->get('service_sensorsRTime')->getUnitsVariables();
		
		//Dates
		$dateActual=new \DateTime();
		$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->modify("midnight")->format("Y-m-d H:i:s");
		$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d")." 23:59:59")->format("Y-m-d H:i:s");	
		$startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->modify("midnight")->format("Y-m-d");	
		$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d")." 23:59:59")->format("Y-m-d H:i:s");	
		
		//Values Chart Stack RTime -->Modify 
		if($buildings)
		{		
			$mappingVariable = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $dFrom, $buildings[0]->getId());
			
			for($i = 1; $i < count($buildings); $i++)  {
				$mappingVariableTmp = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $dFrom, $buildings[$i]->getId());

				if(count($mappingVariableTmp) > 0 ) {
					 //processing the four indicators
					for($d = 0; $d < 4; $d++) {
						$max = -1;
						for($j = 0; $j < count($mappingVariableTmp[$d]["data"]); $j++)  {
							
							$mappingVariable[$d]["data"][$j]["value"] += $mappingVariableTmp[$d]["data"][$j]["value"];
							
							if($max < $mappingVariable[$d]["data"][$j]["value"]) {
								$max = $mappingVariable[$d]["data"][$j]["value"];
							}							
						}
						// updating the max value
						$mappingVariable[$d]["maxValue"] = $max;
					}
				}
			}
		}else{ 
			$mappingVariable = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $dFrom, null);
		}
		
		return $this->render('OptimusOptimusBundle:Dashboard:cityDashboard.html.twig', array('buildings'=>$buildings, "datesBuildings"=>$datesBuildings, "globalRTime"=>$globalRTime, "dataDashboard"=>$dataDashboard, "mappingVariable"=>$mappingVariable, "startDate"=>$startDate, "endDate"=>$endDate, "unitsRTime"=>$unitsRTime));
	}
	
   /* public function indexAction()
    {	
		$em = $this->getDoctrine()->getManager();
		$buildings=$em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		//Data to Charts RTime & User Activity
		$datesBuildings=$this->lastsDatesBuildings($buildings);
		$globalRTime=$this->globalValuesRTime($buildings);
		$dataDashboard=$this->getDataDashboard($buildings);
		$unitsRTime=$this->get('service_sensorsRTime')->getUnitsVariables();
		
		//Dates
		$dateActual=new \DateTime();
		$thisMonday=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d")." 00:00:00";
		$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d H:i:s");
		$startDate=$thisMonday;
		$endDate=$dateActual;
		
		//Values Chart Stack RTime -->Modify 
		if($buildings)
		{			
			$mappingVariable = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $thisMonday, $buildings[0]->getId());
			//dump($mappingVariable);
		}else{ 
			$mappingVariable = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $thisMonday, null);
		}
		
		
		return $this->render('OptimusOptimusBundle:Building:selectBuilding.html.twig', array('buildings'=>$buildings, "datesBuildings"=>$datesBuildings, "globalRTime"=>$globalRTime, "dataDashboard"=>$dataDashboard, "mappingVariable"=>$mappingVariable, "startDate"=>$startDate, "endDate"=>$endDate, "unitsRTime"=>$unitsRTime));			
    }*/
	
	//Get last values real time sensors city
	private function globalValuesRTime($buildings)
	{
		//Dates
		$dateActual=new \DateTime();			
		$dFromThisWeek = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->format("Y-m-d")." 00:00:00";
		$dFromLastWeek=\DateTime::createFromFormat('Y-m-d H:i:s', $dFromThisWeek)->modify("-7 days")->format("Y-m-d")." 00:00:00";
		$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d H:i:s");
				
		$energyCostTW=$co2TW=$energyConsumptionTW=$pREnergyTW=0; //average this week
		$energyCostLW=$co2LW=$energyConsumptionLW=$pREnergyLW=0; //average last week
		
		//Calculate Average this week and last week
		if($buildings)
		{		
			foreach($buildings as $building)
			{
				$dataThisWeek=$this->get('service_sensorsRTime')->getRTTime($dTo, $dFromThisWeek,'', $building->getId());

				$energyCostTW+=$dataThisWeek['Energy cost'];
				$co2TW+=$dataThisWeek['CO2'];
				$energyConsumptionTW+=$dataThisWeek['Energy consumption'];
				$pREnergyTW+=$dataThisWeek['Produced renewable energy'];
				
				$dataLastWeek=$this->get('service_sensorsRTime')->getRTTime($dFromThisWeek, $dFromLastWeek,'', $building->getId());
				
				$energyCostLW+=$dataLastWeek['Energy cost'];
				$co2LW+=$dataLastWeek['CO2'];
				$energyConsumptionLW+=$dataLastWeek['Energy consumption'];
				$pREnergyLW+=$dataLastWeek['Produced renewable energy'];
			}
			/*
			if($energyCostTW!=0) 			$energyCostTW=$energyCostTW/count($buildings);			
			if($co2TW!=0) 					$co2TW=$co2TW/count($buildings);
			if($energyConsumptionTW!=0)		$energyConsumptionTW=$energyConsumptionTW/count($buildings);
			if($pREnergyTW!=0)				$pREnergyTW=$pREnergyTW/count($buildings);
			
			if($energyCostLW!=0) 			$energyCostLW=$energyCostLW/count($buildings);			
			if($co2LW!=0) 					$co2LW=$co2LW/count($buildings);
			if($energyConsumptionLW!=0)		$energyConsumptionLW=$energyConsumptionLW/count($buildings);
			if($pREnergyLW!=0)				$pREnergyLW=$pREnergyLW/count($buildings);
			*/
		}
		
		$globalRTime=array("Energy cost"=>array(0=>$energyCostTW, 1=>$energyCostLW, 2=>$energyCostLW-$energyCostTW), 
							"CO2"=>array(0=>$co2TW, 1=>$co2LW, 2=>$co2LW-$co2TW), 
							"Energy consumption"=>array(0=>$energyConsumptionTW, 1=>$energyConsumptionLW, 2=>$energyConsumptionLW-$energyConsumptionTW), 
							"Produced renewable energy"=>array(0=>$pREnergyTW, 1=>$pREnergyLW, 2=>$pREnergyTW-$pREnergyLW));
		//dump($globalRTime);
		return $globalRTime;
	}
	
	//Last user activity city 
	private function getDataDashboard($buildings)
	{
		$em = $this->getDoctrine()->getManager();
		$aData=array();
		
		$dateActual=new \DateTime();
		$thisMonday=(new \DateTime())->modify("-7 days");
		$lastMonday=\DateTime::createFromFormat('Y-m-d H:i:s', $thisMonday->format("Y-m-d H:i:s"))->modify("-7 days");
		
		$dateActual = $dateActual->modify("-1 days");
		/*$currentMonth=(new \DateTime())->modify("first day of this month");
		$oneMonthBefore=(new \DateTime($currentMonth->format('Y-m-d H:i:s')))->modify("-1 month");
		$twoMonthBefore=(new \DateTime($currentMonth->format('Y-m-d H:i:s')))->modify("-2 month");*/
		
		$days=$this->get('service_dashboard')->getDaysFromDate($thisMonday->format('Y-m-d H:i:s'), $dateActual->format('Y-m-d H:i:s'));
		
		$weekAccepts=$weekDeclines=$weekUnknows=0;
		$monthAccepts=$monthDeclines=$monthUnknows=0;
		$month1Accepts=$month1Declines=$month1Unknows=0;
		$month2Accepts=$month2Declines=$month2Unknows=0;
		
		$aValuesBuilding=array();
		foreach($buildings as $building)
		{
			//this week
			$aWeek=$this->get('service_dashboard')->getDeclinesActiveUnknows($building, $thisMonday->format('Y-m-d H:i:s'), $dateActual->format('Y-m-d H:i:s'));		
			
			$aValuesBuilding[]=$aWeek['allValues'];
			
			$weekAccepts+=$aWeek["accepts"];
			$weekDeclines+=$aWeek["declines"];
			$weekUnknows+=$aWeek["unknows"];
			
			//last week
			$aMonth=$this->get('service_dashboard')->getDeclinesActiveUnknows($building, $lastMonday->format('Y-m-d H:i:s'), $thisMonday->format('Y-m-d H:i:s'));			
			
			$monthAccepts+=$aMonth["accepts"];
			$monthDeclines+=$aMonth["declines"];
			$monthUnknows+=$aMonth["unknows"];
						
			
			//$aData[]=array("id"=>$building->getId(),"name"=>$building->getName(), "aWeek"=>$aWeek, "aMonth"=>$aMonth, "valuesDay"=>$aValuesBuilding);
		}	
		
		$aGlobalWeek=array("declines"=>$weekDeclines, "accepts"=>$weekAccepts, "unknows"=>$weekUnknows);	
		$aGlobalMonth=array("declines"=>$monthDeclines, "accepts"=>$monthAccepts, "unknows"=>$monthUnknows);	
		//$aGlobalMonth1=array("declines"=>$month1Declines, "accepts"=>$month1Accepts, "unknows"=>$month1Unknows);	
		//$aGlobalMonth2=array("declines"=>$month2Declines, "accepts"=>$month2Accepts, "unknows"=>$month2Unknows);	
		
		$valuesDay=array();
		foreach($days as $day)
		{
			$numAccepts=$numDeclines=$numUnknows=0;
			foreach($aValuesBuilding as $value)
			{
				if(count($value)>0)
				{
					foreach($value as $valueDay)
					{
						if($valueDay['date']==$day)
						{
							if($valueDay['status']==0) 		$numUnknows++;
							elseif($valueDay['status']==1)	$numAccepts++;
							elseif($valueDay['status']==2)	$numDeclines++;
						}
					}
				}
			}			
			$valuesDay[]=array("date"=>$day, "accepts"=>$numAccepts, "declines"=>$numDeclines, "unknows"=>$numUnknows);			
		}	
		/*
		$valuesDay[count($valuesDay)-1]["accepts"] = 0;
		$valuesDay[count($valuesDay)-1]["declines"] = 0;
		$valuesDay[count($valuesDay)-1]["unknows"] = 0;
		*/
		
		//array_unshift($aData, array("id"=>null, "name"=>"Global", "aWeek"=>$aGlobalWeek, "aMonth"=>$aGlobalMonth));
		$aData[]=array("id"=>null, "name"=>"Global", "aWeek"=>$aGlobalWeek, "aMonth"=>$aGlobalMonth, "valuesDay"=>$valuesDay);
				
		return $aData;
	}
	
	public function selectOptionsAction()
	{
		$pagina = $this->container->get('security.context')->isGranted('ROLE_ADMIN');
		
		if (!($pagina)) 
		{
			//return 
			return $this->cityDashboardAction();			
		}else
		{		
			return $this->render('OptimusOptimusBundle:Admin:selectOptions.html.twig');
		}
	}
	
	// Dashboard of building with graph lasts indicators --- NEW 
	public function buildingDashboardAction($idBuilding)
	{
		//Info Building
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		$dateActual=new \DateTime();			
		//$startDate=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 days");		
		$startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->modify("midnight")->format("Y-m-d");			
		//$startDateFunction=$startDate->format("Y-m-d H:i:s");
		
		//Get Building
		$em = $this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->findBy(array("id"=>$idBuilding));
		
		//Data average RTime, User Activity & Chart Activity
		$data['dataDashboard']=$this->getDataDashboard($building);
		$data['globalRTime']=$this->globalValuesRTime($building);
		$data['unitsRTime']=$this->get('service_sensorsRTime')->getUnitsVariables();
		
		//Get data for the compound chart stack RTime
		//$lastWeek=$startDate->format("Y-m-d")." 00:00:00";
		//$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d H:i:s");		
		$dFrom = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 day")->modify("midnight")->format("Y-m-d H:i:s");
		$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d")." 23:59:59")->format("Y-m-d H:i:s");	
		$data['mappingVariable'] = $this->get('service_sensorsRTime')->getDataforRenderingChart($dTo, $dFrom, $idBuilding);
		
		//Dates to View
		$data['lastDay']=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d");		
		//$startDateView=$startDate->format("Y-m-d");
		//$endDate=\DateTime::createFromFormat('Y-m-d H:i:s', $startDateFunction)->modify("+7 day");
		$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d")." 23:59:59")->format("Y-m-d H:i:s");	
		//$endDateView=$endDate->format("Y-m-d");		
		//$data['startDate']=$startDateView;
		$data['startDate']=$startDate;
		//$data['endDate']=$endDateView;
		$data['endDate']=$endDate;
		$actualDate=new \DateTime();
		$data['actualDate']=$actualDate->format("Y-m-d");
		$thisMonday=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("monday this week");
		$thisMondayF=$thisMonday->format("Y-m-d H:i:s");
		$endDateWeek=\DateTime::createFromFormat('Y-m-d H:i:s', $thisMondayF)->modify("+6 day");
		
		
		
		//Obtener los ActionsPlans del edificio
		return $this->render('OptimusOptimusBundle:Building:buildingDashboard.html.twig', $data);	
		
	}
	
	//List all action plans --- NEW 
	public function listActionPlansAction($idBuilding)
	{
		//Get Building
		$em = $this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->findBy(array("id"=>$idBuilding));
		
		//Info Building
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		//pedir los ActionPlans del building	
		$data['actionPlans']=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findActionPlansWithSensors($idBuilding);
		
		$dateActual=new \DateTime();			
		$startDate=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("-7 days");				
		$startDateFunction=$startDate->format("Y-m-d H:i:s");
		
		//Dates to View
		$data['lastDay']=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->format("Y-m-d");		
		$startDateView=$startDate->format("Y-m-d");
		$endDate=\DateTime::createFromFormat('Y-m-d H:i:s', $startDateFunction)->modify("+7 day");
		$endDateView=$endDate->format("Y-m-d");		
		$data['startDate']=$startDateView;
		$data['endDate']=$endDateView;
		$actualDate=new \DateTime();
		$data['actualDate']=$actualDate->format("Y-m-d");
		$thisMonday=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("monday this week");
		$thisMondayF=$thisMonday->format("Y-m-d H:i:s");
		$endDateWeek=\DateTime::createFromFormat('Y-m-d H:i:s', $thisMondayF)->modify("+6 day");
		
		//status actions plans
		$data["statusActionPlans"]=$this->getStatusActionsPlans($data['actionPlans'], $dateActual, $thisMonday, $endDateWeek);	

		//last activities in actions plans
		$data['lastsDatesActionsPlans']=$this->lastsDatesActionsPlans($data['actionPlans']);

		//last date data forecasted
		$data['lastDateForecasted']=$this->get('service_data_capturing')->lastForecastedDate($idBuilding);
		
		//Obtener los ActionsPlans del edificio
		return $this->render('OptimusOptimusBundle:Building:listActionPlans.html.twig', $data);	
	}
	
	//Get lasts dates activities for all actionsplans actives
	private function lastsDatesActionsPlans($aActionsPlans)
	{
		$lastsDates=array();
		
		$em = $this->getDoctrine()->getManager();
		$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
		
		if($aActionsPlans != null)
		{
			for($i=0; $i<count($aActionsPlans); $i++)
			{
				$lastsDates[]=$this->get('service_data_capturing')->getLastActionUser($aActionsPlans[$i]->getId(), $user);
			}
		}
		
		return $lastsDates;
	}
	
	//last date data forecasted for all buildings
	private function lastsDatesBuildings($buildings)
	{
		$aDatesBuildings=array();
		if($buildings != null)
		{
			for($i=0; $i<count($buildings); $i++)
			{
				$aDatesBuildings[]=$this->get('service_data_capturing')->lastForecastedDate($buildings[$i]->getId());
			}
		}
		return $aDatesBuildings;
	}
	
	//GET status for all actions plans 
	private function getStatusActionsPlans ($aActionsPlans, $dateActual, $startDate, $endDate)
	{
		$em = $this->getDoctrine()->getManager();
		$aTrafficLight=array();
		if($aActionsPlans != null)
		{
			for($i=0; $i<count($aActionsPlans); $i++)
			{
				if($aActionsPlans[$i]->getType()==1)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_apo_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}elseif($aActionsPlans[$i]->getType()==2)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_apspm_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}elseif($aActionsPlans[$i]->getType()==4)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_apph_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}elseif($aActionsPlans[$i]->getType()==5)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_appvm_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}
				elseif($aActionsPlans[$i]->getType()==6)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_appv_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}
				elseif($aActionsPlans[$i]->getType()==7)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_apsource_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}
				elseif($aActionsPlans[$i]->getType()==8)
				{
					$aTrafficLight[$aActionsPlans[$i]->getId()]=$this->get('service_apeconomizer_presenter')->getTrafficLight($aActionsPlans[$i]->getId(), $dateActual, $startDate, $endDate);	
					
				}
			}
		}
		return $aTrafficLight;
	}
	
	private function getIDsSensorsGraphStacks($idBuilding)
	{
		$em = $this->getDoctrine()->getManager();
		$sensors=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding));
		
		$aSensorsIds=array();
		if($sensors)
		{
			foreach($sensors as $sensor)
			{
				$aSensorsIds[]=$sensor->getFkSensor();
			}
		}		
		return $aSensorsIds;
	}
	
	private function getMappingVariables($idBuilding)
	{
		$em = $this->getDoctrine()->getManager();
		$sensorsRTime=$this->get('service_sensorsRTime')->getVariablesGraphStack();
		$aMapping=array();
		
		foreach($sensorsRTime as $sensor)
		{
			$sensorMapping=$em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>$sensor['name']));
			
			if($sensorMapping)		$aMapping[]=array("name"=>$sensor['name'], "color"=>$sensor['color'], "idSensor"=>$sensorMapping[0]->getFkSensor());
			else 					$aMapping[]=array("name"=>$sensor['name'], "color"=>$sensor['color'], "idSensor"=>null);
		}
		
		return $aMapping;
	}

	private function getMaxMinValuesMapping($mappingVariable, $dataFinal)
	{
		$aMapping=array();
		foreach($mappingVariable as $mappingSensor)
		{
			$maxValue=$minValue=0;
				
			if ($mappingSensor['idSensor'] !=null)
			{
				$i=0;
				foreach($dataFinal as $variable)
				{					
					if($variable['idSensor']==$mappingSensor['idSensor']->getId())
					{						
						if($maxValue<$variable['max']) 		$maxValue=$variable['max'];
						if($i=0)							$minValue=$variable['min'];
						elseif($minValue>$variable['min']) 	$minValue=$variable['min'];
					}
					$i++;
				}
			}
			$aMapping[]=array("name"=>$mappingSensor['name'], "color"=>$mappingSensor['color'], "idSensor"=>$mappingSensor['idSensor'], "maxValue"=>$maxValue, "minValue"=>$minValue);
		}
		return $aMapping;
	}
	
	
}