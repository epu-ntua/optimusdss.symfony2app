<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APPVOutput;
use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class ServiceEventos {
	 
    protected $em;
 
    public function __construct(EntityManager $em)
    {
		$this->em=$em;
    }
	
	public function createEvent($date, $user, $event)
	{
		//insertar evento
	}
	
	public function lastsEvent()
	{
		//get events desc limit 5
	}
}

?>