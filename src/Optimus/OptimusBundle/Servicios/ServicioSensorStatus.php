<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APPVOutput;
use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\Sensor;

class ServicioSensorStatus {
	 
    protected $em;
	protected $ontologia;	
	
    public function __construct(EntityManager $em, ServiceOntologia $ontologia)
    {
		$this->em=$em;
		$this->ontologia=$ontologia;
    }
	
	public function checkStatus()
	{
		$sensors=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findAll();
		
		foreach($sensors as $sensor)
		{	
			$url = $sensor->getUrl();
			
			$status = "Checking...";
			$lastData = $sensor->getlastData();
			
			set_time_limit(0);
			ini_set('memory_limit','64M');
			$ret = $this->ontologia->checkSensor($url, $lastData);
			
			if(count($ret) > 0) {
				$lastData = $ret[0]["datetime"];
				$status = "OK";
			} else {
				$ret = $this->ontologia->lastDataSensor($url, $lastData);
				
				if(count($ret) > 0) {
					$lastData = $ret[0]["datetime"];
				}
				
				$status = "No receiving data";
			}
		
			$sensor->setStatus($status);
			$sensor->setlastData($lastData);
			
			$this->em->persist($sensor);
            $this->em->flush();
		}
	}
}

?>