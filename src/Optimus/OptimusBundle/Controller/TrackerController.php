<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TrackerController extends Controller
{
	public function trackerAction()
	{	
		return $this->render('OptimusOptimusBundle:Tracker:tracker.html.twig');
	}
}

?>