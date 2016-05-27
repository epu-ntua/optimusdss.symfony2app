<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventsController extends Controller
{
	public function lastsEventsAction ($idBuilding, $idActionPlan=null)
	{
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		$data['lastsEvents']=$this->get('service_event')->lastsEvent($idBuilding, $idActionPlan);
		
		return $this->render('OptimusOptimusBundle:Events:userActivity.html.twig', $data);
	}
}

?>