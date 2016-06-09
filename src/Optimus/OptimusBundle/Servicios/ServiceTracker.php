<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\CurrentStatus;

class ServiceTracker {
	 
    protected $em;	
	
    public function __construct(EntityManager $em)
    {
		$this->em=$em;		
    }
	
	public function currentStatusTracker()
	{	
		$dateActual=new \DateTime();
		
		$currentStatus=new CurrentStatus();
		$currentStatus->setDate($dateActual);
		$currentStatus->setStatusEConsumption(1);			
		$currentStatus->setStatusCO2(2);			
		$currentStatus->setStatusECost(3);			
		$currentStatus->setStatusREUse(4);			
		$this->em->persist($currentStatus);
		$this->em->flush();
	}
}