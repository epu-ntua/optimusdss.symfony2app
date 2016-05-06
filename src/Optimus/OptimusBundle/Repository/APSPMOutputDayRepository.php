<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APSPMOutputDayRepository extends EntityRepository{

    public function getDQLPredictionDate($idCalculation, $day)
	{		
		$dqlQuery = 'SELECT apspmoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apspmoutputday';
        $dqlQuery .= " WHERE apspmoutputday.date = '".$day."' and  apspmoutputday.fkApCalculation='".$idCalculation."' ";
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
		$dqlQuery = 'SELECT apspmoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apspmoutputday';
        $dqlQuery .= " WHERE apspmoutputday.date = '".$day."' ";
		$dqlQuery .= ' ORDER BY apspmoutputday.fkApCalculation DESC';
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