<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APPVMOutputDayRepository extends EntityRepository{

    public function getDQLPredictionDate($idCalculation, $day)
	{		
		$dqlQuery = 'SELECT appvmoutputday';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvmoutputday';
        $dqlQuery .= " WHERE appvmoutputday.date = '".$day."' and  appvmoutputday.fkApCalculation='".$idCalculation."' ";
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
		$dqlQuery = 'SELECT appvmoutput';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvmoutput';
        $dqlQuery .= " WHERE appvmoutput.date = '".$day."' ";
		$dqlQuery .= ' ORDER BY appvmoutput.fkApCalculation DESC';
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