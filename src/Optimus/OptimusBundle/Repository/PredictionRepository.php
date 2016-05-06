<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class PredictionRepository extends EntityRepository{

    public function getDQLPredictionDate($day, $idBuilding)
	{		
		$dqlQuery = 'SELECT prediction ';
        $dqlQuery .= ' FROM '.$this->_entityName.' prediction';
        $dqlQuery .= " WHERE '".$day."'>=prediction.dateCreate and '".$day."'<=DATE_ADD(prediction.dateCreate, 7, 'day')";
        $dqlQuery .= " and '".$idBuilding."'=prediction.fk_Building";
        $dqlQuery .= ' ORDER BY prediction.dateUser DESC';
        return $dqlQuery;	
    }
	
	public function findPredictionByDate($day, $idBuilding){
      
        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLPredictionDate($day, $idBuilding);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);		
		
        return $query->getResult();
    }
    
}

?>