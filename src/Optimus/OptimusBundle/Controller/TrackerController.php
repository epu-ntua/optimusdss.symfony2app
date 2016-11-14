<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

use Optimus\OptimusBundle\Entity\Tracker;
use Optimus\OptimusBundle\Form\TrackerType;

class TrackerController extends Controller
{
	public function trackerAction()
	{	
		$em=$this->getDoctrine()->getManager();
		$dataTracker=$em->getRepository('OptimusOptimusBundle:Tracker')->find(1);
		$data['dataTracker']=$dataTracker;
		
		//Get max user target 
		$data['maxValue']=max($dataTracker->getTargetEConsumption(), $dataTracker->getTargetCO2(), $dataTracker->getTargetECost(), $dataTracker->getTargetREUse());
		
		//$this->get('service_tracker')->currentStatusTracker();
		
		$data['currentStatus']=$em->getRepository('OptimusOptimusBundle:CurrentStatus')->findBy(array(), array('date'=>'desc'));
		$data['city_name']=$em->getRepository('OptimusOptimusBundle:Building')->find(1)->getCity();
		return $this->render('OptimusOptimusBundle:Tracker:tracker.html.twig', $data);
	}
	
	public function valuesTrackerAction(Request $request)
	{
		$em=$this->getDoctrine()->getManager();
		$tracker=$em->getRepository('OptimusOptimusBundle:Tracker')->find(1);

		if(!$tracker)	$tracker = new Tracker();
			
        $form = $this->createForm(new TrackerType(), $tracker);		
		$form->handleRequest($request);
		
		if ($form->isValid()) 
		{				
			$em->flush();			
			return $this->redirect($this->generateUrl('adminBuilding'));
		}
		return $this->render('OptimusOptimusBundle:Tracker:valuesTracker.html.twig', array(
            'entity' => $tracker,
            'form'   => $form->createView()
        ));
	}
	
}

?>