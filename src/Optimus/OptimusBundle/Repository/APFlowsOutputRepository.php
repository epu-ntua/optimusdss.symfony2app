<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APFlowsOutputRepository extends EntityRepository{

    public function getDQLOutputsDate($calculation, $dayStart, $dayEnd)
	{		
		$dqlQuery = 'SELECT apflowsoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apflowsoutput';
        $dqlQuery .= " WHERE apflowsoutput.hour_timestamp >= '".$dayStart."' and  apflowsoutput.hour_timestamp<='".$dayEnd."' ";
        $dqlQuery .= " and apflowsoutput.fkApCalculation = ".$calculation." ";
        $dqlQuery .= ' ORDER BY apflowsoutput.hour_timestamp ASC';
        return $dqlQuery;	
    }
	
	public function findFlowsOutputsByDate($calculation, $day){
        $em = $this->getEntityManager();
		$date=explode(" ", $day);
		$dayStart=$day;
		$dayEnd=$date[0]." 23:59:00";
        $dqlQuery = $this->getDQLOutputsDate($calculation, $dayStart, $dayEnd);
        $query = $em->createQuery($dqlQuery);
			//$query->setMaxResults(1);		 
        return $query->getResult();
    }

	public function getDQLLastOutputs($calculation)
	{		
		$dqlQuery = 'SELECT apflowsoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apflowsoutput ';
        $dqlQuery .= " WHERE apflowsoutput.fkApCalculation = ".$calculation." ";
        $dqlQuery .= ' ORDER BY apflowsoutput.hour_timestamp DESC ';
        return $dqlQuery;	
    }
	
	public function findLastOutputByCalculation($calculation){
      
        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLLastOutputs($calculation);
		//dump($dqlQuery);
        $query = $em->createQuery($dqlQuery);
		$query->setMaxResults(1);
        return $query->getResult();
    }
    
}

?>