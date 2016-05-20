<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APFlowsOutputDayRepository extends EntityRepository{

    public function getDQLPredictionDate($idCalculation, $day)
	{		
		$dqlQuery = 'SELECT apflowsoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' apflowsoutputday';
        $dqlQuery .= " WHERE apflowsoutputday.date = '".$day."' and  apflowsoutputday.fkApCalculation='".$idCalculation."' ";
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