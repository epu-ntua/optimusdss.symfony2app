<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APSensorsRepository extends EntityRepository{

    public function getDQLAPSensorsOrder($fkActionPlan)
	{		
		$dqlQuery = 'SELECT apsensors ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apsensors';
        $dqlQuery .= " WHERE apsensors.fk_actionplan =".$fkActionPlan."";
		//$dqlQuery .= " GROUP BY apsensors.fk_BuildingPartitioning";
        $dqlQuery .= " ORDER BY apsensors.fk_BuildingPartitioning ASC, apsensors.orderSensor ASC";
       
        return $dqlQuery;	
    }
	
	public function getAPSensorsOrder($fkActionPlan){
      
        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLAPSensorsOrder($fkActionPlan);
        $query = $em->createQuery($dqlQuery);       
		
        return $query->getResult();
    }
    
}

?>