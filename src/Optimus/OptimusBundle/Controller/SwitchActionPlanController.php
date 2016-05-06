<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Optimus\OptimusBundle\Entity\APSwitchOutputDay;

class SwitchActionPlanController extends Controller
{			
    public function indexAction($idBuilding, $idAPType='', $from='', $to='', $timeSelected='')
    {	
		//Seleccionar el Action Plan correspondiente		
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
		
		//building
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		$data['idAPType']=$idAPType;
		
		//Action Plan
		$em = $this->getDoctrine()->getManager();	
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>$idAPType));
		
		if($actionPlan != null)
		{
			$idActionPlan=$actionPlan[0]->getId();
			
			$data['idActionPlan']=$idActionPlan;
			$data['dataActionPlan_name'] = $actionPlan[0]->getName();
			$data['dataActionPlan_description'] = $actionPlan[0]->getDescription();
			$data['dataActionPlan_lastCalculation']=$this->get('service_data_capturing')->getLastCalculated($idActionPlan);
			
			$data['dataActionPlan'] = $this->get('service_apph_presenter')->getDataValues($startDateFunction, $idActionPlan);
		
			//Partitions buildings
			$data['allPartitions']=$this->getPartitions($idBuilding, $idActionPlan, $idAPType);			
		}		
		
		//Tree
		$tree=$this->getPartitionsBuilding($idBuilding);
		$data['tree']=str_replace("'","\"",$tree);
		
		return $this->render('OptimusOptimusBundle:SwitchActionPlan:switchOnOffActionPlan.html.twig', $data);		
    }
	
	private function getPartitionsBuilding($idBuilding)
	{		
		$em = $this->getDoctrine()->getManager();
			
		$partitionBuilding=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		$str='';		
		
		if($partitionBuilding)
		{	
			$str='[';
			$i=0;
			foreach($partitionBuilding as $partition)
			{				
				if($partition->getFkBuildingPartitioning()==null)
					$str.="{ 'id':'".$partition->getId()."', 'parent':'#', 'text':'".$partition->getPartitionName()."','children' : false}";
				else {
					
					$str.=",{ 'id':'".$partition->getId()."', 'parent':'".$partition->getFkBuildingPartitioning()->getId()."', 'text':'".$partition->getPartitionName()."'}";
				}
			}
			$str.=']';
		}

		return $str;
	}
	
	private function getLevelOfPartition($partition, $partitionBuilding) 
	{
		$level = 1;
		
		$parent = $partition->getFkBuildingPartitioning();
		
		while($parent != null) {
			
			foreach($partitionBuilding as $par)
			{
				if($par->getId() == $parent->getId() ) {
					$parent = $par->getFkBuildingPartitioning();
					break;
				}
			}
			
			
			$level++;
		}
		
		
		return $level;
	}
	
	private function getPartitions($idBuilding, $idActionPlan, $idAPType)
	{
		$allPartitions=array();
		$child=false;
		$sensors=false;
		
		$em = $this->getDoctrine()->getManager();
		
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>$idAPType));		
		$idActionPlan=$actionPlan[0]->getId();
		
		$partitionsBuilding=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		if($partitionsBuilding)
		{	
			foreach($partitionsBuilding as $partition)
			{				
				$childs=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_BuildingPartitioning"=>$partition->getId()));
				if($childs)		$child=true;
				else 			$child=false;
				
				$level = $this->getLevelOfPartition($partition, $partitionsBuilding);
				
				//comprobar si tiene sensores asociados
				$apSensors=$em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_BuildingPartitioning"=>$partition->getId(), "fk_actionplan"=>$idActionPlan));				
				if($apSensors)		$sensors=true;
				else 				$sensors=false;
				
				$allPartitions[]=array("id"=>$partition->getId(), "childs"=>$child, "partition"=>$partition, "sensors"=>$sensors, "level"=>$level);
			}			
		}
			
		return $allPartitions;
	}
	
	//cambia el estado de un dia en el action plan 
	public function changeStatusDayAction(Request $request, $idOutputDay)
	{	
		$em = $this->getDoctrine()->getManager();		
		$outputDay=$em->getRepository('OptimusOptimusBundle:APSwitchOutputDay')->findBy(array("id"=>$idOutputDay));
		$elementsForm=array();
		
		if($outputDay)
		{			
			parse_str($request->request->get('data'), $elementsForm);			
			$status=(int)$elementsForm['filter'];
			$outputDay[0]->setStatus($status);
			$em->flush();
			
			//event change status
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			
			if($status==0) 			$newStatus="unknowns";
			elseif($status==1) 		$newStatus="accepts";
			else 					$newStatus="declines";
			
			$textEvent=" Scheduling the on/off of the heating system ";
			$context="Action plan - Scheduling the on/off of the heating system";
			$visible=1;
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$idActionPlan=(int)$elementsForm['idActionPlan'];
			
			$this->get('service_event')->createEvent($user, $textEvent, $context, $idActionPlan, 1, $ip, $request->request->get('idBuilding'), $newStatus);
		}
		
		return new Response("ok");
	}
	
	//Strategy by Day
	public function changeStrategyAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$idOutputDay=$request->request->get('idOutputDay');
		$type=$request->request->get('type');
		
		$outputDay=$em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findBy(array("id"=>$idOutputDay));
		if($outputDay)
		{
			foreach($outputDay as $output)
			{
				$output->setStrategy($type);
				$em->flush();
				
				//event change strategy
			}
		}
		
		return new Response("ok");
	}
	
}