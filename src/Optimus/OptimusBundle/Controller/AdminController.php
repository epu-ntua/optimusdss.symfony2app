<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends Controller
{			
    /*public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
		$buildings=$em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		return $this->render('OptimusOptimusBundle:Admin:adminBuildings.html.twig', array('buildings'=>$buildings));
    }*/

	/*public function partitionAction()
	{
		return $this->render('OptimusOptimusBundle:Admin:adminPartitions.html.twig');
	}
	
	public function sensorAction()
	{
		return $this->render('OptimusOptimusBundle:Admin:adminSensors.html.twig');
	}
	
	public function actionPlanAction()
	{
		return $this->render('OptimusOptimusBundle:Admin:adminActionPlans.html.twig');
	}*/
}