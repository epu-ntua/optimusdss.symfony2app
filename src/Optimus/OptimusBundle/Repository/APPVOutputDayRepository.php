<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APPVOutputDayRepository extends EntityRepository{

    public function getDQLPredictionDate($idCalculation, $day)
	{		
		$dqlQuery = 'SELECT appvoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvoutputday';
        $dqlQuery .= " WHERE appvoutputday.date = '".$day."' and  appvoutputday.fkApCalculation='".$idCalculation."' ";
        return $dqlQuery;	
    }
	
	public function findOutputByDay($idCalculation, $day){
      
        $em = $this->getEntityManager();
		
        $dqlQuery = $this->getDQLPredictionDate($idCalculation, $day);
        $query = $em->createQuery($dqlQuery);
			//$query->setMaxResults(1);		
		
        return $query->getResult();
    }    
}

?>