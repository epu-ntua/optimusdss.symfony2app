<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APOOutputDayRepository extends EntityRepository{

    public function getDQLPredictionDate($idCalculation, $day)
	{		
		$dqlQuery = 'SELECT apooutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apooutputday';
        $dqlQuery .= " WHERE apooutputday.date = '".$day."' and  apooutputday.fkApCalculation='".$idCalculation."' ";
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
		$dqlQuery = 'SELECT apooutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apooutputday';
        $dqlQuery .= " WHERE apooutputday.date = '".$day."' ";
		$dqlQuery .= ' ORDER BY apooutputday.fkApCalculation DESC';
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