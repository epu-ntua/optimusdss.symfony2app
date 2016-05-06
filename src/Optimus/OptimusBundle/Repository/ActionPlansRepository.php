<?php
namespace Optimus\OptimusBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Response;

class ActionPlansRepository extends EntityRepository{

    public function getDQLActionPlansWithSensors($idBuilding)
	{				
		$dqlQuery = 'SELECT DISTINCT actionplans ';
        $dqlQuery .= ' FROM '.$this->_entityName.' actionplans,  Optimus\OptimusBundle\Entity\APSensors apsensors';
        //$dqlQuery .= " JOIN apsensors.fk_actionplan";
        $dqlQuery .= " WHERE ".$idBuilding."=actionplans.fk_Building AND apsensors.fk_actionplan=actionplans.id and actionplans.status=1";
		//dump($dqlQuery);		
        return $dqlQuery;		
    }
	
	public function findActionPlansWithSensors($idBuilding)
	{
		$em = $this->getEntityManager();		
		// IMPORTANT: By default Doctrine does not support all the functions of a specific vendor, such as the DATE_ADD. 
		$dqlQuery = $this->getDQLActionPlansWithSensors($idBuilding);
        $query = $em->createQuery($dqlQuery);
        //$query->setMaxResults(1);
        return $query->getResult();
    }
	
	 public function getDQLStatusActionPlan($idActionPlan)
	{				
		$dqlQuery = 'SELECT actionplans';
        $dqlQuery .= ' FROM '.$this->_entityName.' actionplans,  Optimus\OptimusBundle\Entity\APSensors apsensors';
        //$dqlQuery .= " JOIN apsensors.fk_actionplan";
        $dqlQuery .= " WHERE ".$idActionPlan."=actionplans.id AND apsensors.fk_actionplan=actionplans.id";
		//dump($dqlQuery);		
        return $dqlQuery;		
    }
	
	public function findActionPlanWithSensors($idActionPlan)
	{
		$em = $this->getEntityManager();		
		$dqlQuery = $this->getDQLStatusActionPlan($idActionPlan);
        $query = $em->createQuery($dqlQuery);
        return $query->getResult();
	}
}

?>