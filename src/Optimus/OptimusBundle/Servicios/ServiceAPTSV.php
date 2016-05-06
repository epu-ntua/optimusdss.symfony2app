<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class ServiceAPTSV {
 
    protected $em;
 
    public function __construct(EntityManager $em)
    {       
		$this->em=$em;
    }
	
	
	
}
?>