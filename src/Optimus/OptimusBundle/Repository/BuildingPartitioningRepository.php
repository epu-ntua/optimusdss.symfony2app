<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class BuildingPartitioningRepository extends EntityRepository{

    public function deleteDQLPartitioning($idPartition)
	{		
		$dqlQuery = 'DELETE ';
        $dqlQuery .= ' FROM '.$this->_entityName.' buildingpartitioning';
        $dqlQuery .= " WHERE '".$idPartition."'=buildingpartitioning.id ";      
        return $dqlQuery;	
    }
	
	public function deletePartitioning($idPartition){
      
        $em = $this->getEntityManager();
        $dqlQuery = $this->deleteDQLPartitioning($idPartition);
        $query = $em->createQuery($dqlQuery);
       // $query->setMaxResults(1);		
		
        //return $query->getResult();
    }
    
}

?>