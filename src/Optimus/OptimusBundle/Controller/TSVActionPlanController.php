<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class TSVActionPlanController extends Controller
{
	 public function indexAction($idBuilding, $from='', $to='', $timeSelected='')
    {	
		//Seleccionar el Action Plan correspondiente		
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
		
		//building
		$data['idBuilding']=$idBuilding;
		
		//Action Plan
		$em = $this->getDoctrine()->getManager();	
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>4));		
		$idActionPlan=$actionPlan[0]->getId();
		$data['dataActionPlan'] = $this->get('ServiceAPPV')->getDataPVActionPlan($startDateFunction, $idActionPlan);
		
		//Partitions buildings
		$data['allPartitions']=$this->getPartitions($idBuilding, $idActionPlan);		
		
		//Tree
		$tree=$this->getPartitionsBuilding($idBuilding);
		$data['tree']=str_replace("'","\"",$tree);
		
		return $this->render('OptimusOptimusBundle:TSVActionPlan:TSVActionPlan.html.twig', $data);		
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
					$str.="{ 'id':'".$partition->getId()."', 'parent':'#', 'text':'".$partition->getPartitionName()."','children' : false }";
				else
					$str.=",{ 'id':'".$partition->getId()."', 'parent':'".$partition->getFkBuildingPartitioning()->getId()."', 'text':'".$partition->getPartitionName()."' }";
			}
			$str.=']';
		}
		return $str;
	}
	
	private function getPartitions($idBuilding, $idActionPlan)
	{
		$allPartitions=array();
		$child=false;
		$sensors=false;
		
		$em = $this->getDoctrine()->getManager();
		
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>2));		
		$idActionPlan=$actionPlan[0]->getId();
		
		$partitionsBuilding=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		if($partitionsBuilding)
		{	
			foreach($partitionsBuilding as $partition)
			{				
				$childs=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_BuildingPartitioning"=>$partition->getId()));
				if($childs)		$child=true;
				else 			$child=false;
				
				//comprobar si tiene sensores asociados
				$apSensors=$em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_BuildingPartitioning"=>$partition->getId(), "fk_actionplan"=>$idActionPlan));				
				if($apSensors)		$sensors=true;
				else 				$sensors=false;
				
				$allPartitions[]=array("id"=>$partition->getId(), "childs"=>$child, "partition"=>$partition, "sensors"=>$sensors);
			}			
		}
		
		return $allPartitions;
	}
	
	//cambia el estado de un dia en el action plan 
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
			
			//event change status
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

?>