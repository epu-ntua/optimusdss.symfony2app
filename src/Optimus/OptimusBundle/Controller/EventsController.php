<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventsController extends Controller
{
	public function lastsEventsAction ($idBuilding, $idActionPlan=null)
	{
		$data['lastEvents']=$this->get('service_event')->lastsEvent($idBuilding, $idActionPlan);
		
		return $this->render('OptimusOptimusBundle:Events:lastEvents.html.twig', $data);
	}
}

?>