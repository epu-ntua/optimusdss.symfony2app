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
		$dataCurrentStatus=$em->getRepository('OptimusOptimusBundle:CurrentStatus')->findBy(array(), array('date'=>'desc'));
		
		return $this->render('OptimusOptimusBundle:Tracker:tracker.html.twig');
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