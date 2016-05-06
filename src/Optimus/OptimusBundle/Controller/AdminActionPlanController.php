<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

use Optimus\OptimusBundle\Entity\ActionPlans;
use Optimus\OptimusBundle\Form\ActionPlansType;


class AdminActionPlanController extends Controller
{
	public function adminActionPlansAction($idBuilding)
	{
		$em = $this->getDoctrine()->getManager();		
		$actionPlans=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding));
	
		$aActionPlans=$this->getStatusActionPlans($actionPlans);
		
		$nameBuilding=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		return $this->render('OptimusOptimusBundle:Admin:adminActionPlans.html.twig', array("actionPlans"=>$aActionPlans, "idBuilding"=>$idBuilding, "nameBuilding"=>$nameBuilding ) );		
	}
	
	private function getStatusActionPlans($actionPlans)
	{
		$aActionPlans=array();
		
		if($actionPlans)
		{
			$em = $this->getDoctrine()->getManager();
			
			foreach($actionPlans as $aPlan)
			{
				$status=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findActionPlanWithSensors($aPlan->getId());
				
				if($status!=null) 	$setStatus=1;
				else 					$setStatus=0;
				$aActionPlans[]=array("id"=>$aPlan->getId(), "name"=>$aPlan->getName(), "description"=>$aPlan->getDescription(), "status"=>$aPlan->getStatus(), "mappings"=>$setStatus);
			}
		}
		return $aActionPlans;
	}
	
	public function editAction($idBuilding, $idActionPlan)
	{
		$em=$this->getDoctrine()->getManager();
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->find($idActionPlan);       
        $form    = $this->createForm(new ActionPlansType(), $actionPlan);
				
		return $this->render('OptimusOptimusBundle:ActionPlans:edit.html.twig', array(
            'entity' => $actionPlan,
            'form'   => $form->createView(),
			'idBuilding' =>$idBuilding,
			'idActionPlan' =>$idActionPlan
        ));		
	}
	
	public function saveAction(Request $request, $idBuilding, $idActionPlan)
	{
		$em=$this->getDoctrine()->getManager();
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->find($idActionPlan);       
        $form    = $this->createForm(new ActionPlansType(), $actionPlan);
		
		$form->handleRequest($request);

        if ($form->isValid()) {            
            $em->persist($actionPlan);
            $em->flush();
			
			//create event
			/*$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$this->get('service_event')->createEvent($user, "Edit sensor", "Sensor", $idActionPlan, 1, $ip, $idBuilding, "edit" );*/
			
			return $this->redirect($this->generateUrl("adminActionPlans", array('idBuilding' => $idBuilding)));           
        }else{
			return false;
		}
		
	}
}