<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class SensorRepository extends EntityRepository{

    public function getDQLSensorsFromArray()
	{		
		$dqlQuery = 'SELECT sensor ';
        $dqlQuery .= ' FROM '.$this->_entityName.' sensor';
        $dqlQuery .= " WHERE sensor.id IN (:opc)";
        return $dqlQuery;	
    }
	
	public function getSensorsFromArray($aIdsSensors){
      
        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLSensorsFromArray();
        $query = $em->createQuery($dqlQuery);        	
		$query->setParameter('opc', $aIdsSensors);
		
        return $query->getResult();
    }
    
}

?>