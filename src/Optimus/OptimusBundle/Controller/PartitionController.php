<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Optimus\OptimusBundle\Entity\BuildingPartitioning; 

class PartitionController extends Controller
{			
    public function indexAction($idBuilding)
    {	
		$tree=$this->getPartitionsBuilding($idBuilding);
		$tree=str_replace("'","\"",$tree);
		
		$nameBuilding=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
				
		return $this->render('OptimusOptimusBundle:Admin:adminPartitions.html.twig', array("tree"=>$tree, "idBuilding"=>$idBuilding, "nameBuilding"=>$nameBuilding));
    }
	
	public function createAction(Request $request)
	{
		$content = $request->request->get('content');
		$tree=json_decode($content, true);
		$em = $this->getDoctrine()->getManager();
			
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($request->request->get('idBuilding'));
		
		if(!empty($tree))
		{
			$parent='';
			foreach($tree as $level)
			{			
				$partition=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->find($level['id']);
				
				if($partition)
				{				
					//existe -->update
					$partition->setPartitionName($level['text']);
					//si parent==# -->null, else buscar partitionParent
					if($level['parent']!='#') 					
					{
						$parent=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->find($level['parent']);
						$partition->setFkBuildingPartitioning($parent);
					}
					$em->flush();
					
					$parentReal=$partition;
				}else{					
					//no existe 
					$partition=new BuildingPartitioning();
					$partition->setPartitionName($level['text']);
					$partition->setFkBuilding($building);
					if($level['parent']!='#') 
					{
						$parent=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->find($level['parent']);
						if($parent)
						{
							$partition->setFkBuildingPartitioning($parent);
							$parent->addChild($partition);
						}else{
							
							$partition->setFkBuildingPartitioning($parentReal);
							$parentReal->addChild($partition);
						}
					}					
					$em->persist($partition);
					$em->flush();
					
					$parentReal=$partition;
				}				
			}
		}else{
			$partition=new BuildingPartitioning();
			$partition->setPartitionName('Building');
			$partition->setFkBuilding($building);							
			$em->persist($partition);
			$em->flush();
		}
        
		return new Response(json_encode($tree)); 
	}
	
	public function deleteAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$idPartition=$request->request->get('partition');	
		$partition=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->find($idPartition);
		
		if($partition)
		{		
			if($partition->getFkBuildingPartitioning()!=null)
			{
				//Cuando se elimina sus hijos tb se eliminan
				$em->remove($partition);
				$em->flush();

			}else{ //Building
				//Eliminamos todos sus hijos
				$childs = $partition->getChildren();				
				foreach ($childs as $child )
				{					
					$partition->removeChild($child);
					$em->flush();
				}			
			}
		}
		return new Response("ok");
	}
	
	public function getPartitionsBuilding($idBuilding)
	{
		//dump($idBuilding);
		$em = $this->getDoctrine()->getManager();
		
		//$building=$em->getRepository('OptimusOptimusBundle:Building')->findBy(array("id"=>$idBuilding));
		
		$partitionBuilding=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		$str='';
		
		//dump($partitionBuilding);
		
		if($partitionBuilding)
		{	
			$str='[';
			$i=0;
			foreach($partitionBuilding as $partition)
			{
				//dump($partition->getFkBuildingPartitioning());
				if($partition->getFkBuildingPartitioning()==null)
					$str.="{ 'id':'".$partition->getId()."', 'parent':'#', 'text':'".$partition->getPartitionName()."','children' : false }";
				else
					$str.=",{ 'id':'".$partition->getId()."', 'parent':'".$partition->getFkBuildingPartitioning()->getId()."', 'text':'".$partition->getPartitionName()."' }";
			}
			$str.=']';
		}
		return $str;
	}
	
	public function getStructureAndSensors()
	{
	
	}
}