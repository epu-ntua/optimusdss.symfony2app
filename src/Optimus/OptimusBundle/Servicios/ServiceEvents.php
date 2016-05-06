<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\Events;

class ServiceEvents {
	 
    protected $em;
 
    public function __construct(EntityManager $em)
    {
		$this->em=$em;
    }
	
	//insert event to BD 
	public function createEvent($user,  $textEvent, $context, $id_context, $visible, $ip,  $idBuilding, $action)
	{		
		//insertar evento
		$date=new \DateTime();		
		$fkBuilding=$this->getBuilding($idBuilding);
		
		$event = new Events();
		$event->setDate($date);
		
		if($user!=NULL)			$event->setFkUser($user);
		
		$event->setTextEvent($textEvent);
		$event->setContext($context);
		
		if($id_context!=NULL)	$event->setIdContext($id_context);
		
		$event->setIp($ip);
		$event->setVisible($visible);
		$event->setFkBuilding($fkBuilding);
		$event->setAction($action);
	 		
		$this->em->persist($event);
		$this->em->flush();
	}
	
	//return last 5 events	
	public function lastsEvent($idBuilding, $idActionPlan=null)
	{
		//get events desc limit 5 where visible ='yes'
		$lastEvents=$this->em->getRepository('OptimusOptimusBundle:Events')->findLastEvents($idBuilding, $idActionPlan);		
		return $lastEvents;
	}
		
	private function getBuilding($idBuilding)
	{
		$building=$this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		if($building)
		{
			return $building;
		}else return null;
	}
	
}

?>