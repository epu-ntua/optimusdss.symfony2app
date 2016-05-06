<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APEconomizerOutputDayRepository extends EntityRepository{

    public function getDQLPredictionDate($idCalculation, $day)
	{		
		$dqlQuery = 'SELECT apeconoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apeconoutputday';
        $dqlQuery .= " WHERE apeconoutputday.date = '".$day."' and  apeconoutputday.fkApCalculation='".$idCalculation."' ";
        return $dqlQuery;	
    }
	
	public function findOutputByDay($idCalculation, $day){
      
        $em = $this->getEntityManager();
		
        $dqlQuery = $this->getDQLPredictionDate($idCalculation, $day);
        $query = $em->createQuery($dqlQuery);
			//$query->setMaxResults(1);		
		
        return $query->getResult();
    }    
	
	
	
	public function getDQLLastOutputByDay($day)
	{		
		$dqlQuery = 'SELECT apeconoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apeconoutputday';
        $dqlQuery .= " WHERE apeconoutputday.date = '".$day."' ";
		$dqlQuery .= ' ORDER BY apeconoutputday.fkApCalculation DESC';
        return $dqlQuery;	
    }
	
	public function findLastOutputByDay($day){
      
        $em = $this->getEntityManager();
		
        $dqlQuery = $this->getDQLLastOutputByDay($day);
        $query = $em->createQuery($dqlQuery);
		$query->setMaxResults(1);		
		
        return $query->getResult();
    }    
}

?>