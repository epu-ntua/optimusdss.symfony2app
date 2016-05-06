<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APEconomizerOutputRepository extends EntityRepository{

    public function getDQLOutputsDate($calculation, $dayStart, $dayEnd)
	{		
		$dqlQuery = 'SELECT apeconoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apeconoutput';
        $dqlQuery .= " WHERE apeconoutput.hour >= '".$dayStart."' and  apeconoutput.hour<='".$dayEnd."' ";
        $dqlQuery .= " and apeconoutput.fkApCalculation = ".$calculation." ";
        $dqlQuery .= ' ORDER BY apeconoutput.hour ASC';
        return $dqlQuery;	
    }
	
	public function findOutputsByDate($calculation, $day){
      
        $em = $this->getEntityManager();
		$date=explode(" ", $day);
		$dayStart=$day;
		$dayEnd=$date[0]." 23:59:00";
		
        $dqlQuery = $this->getDQLOutputsDate($calculation, $dayStart, $dayEnd);
        $query = $em->createQuery($dqlQuery);
			//$query->setMaxResults(1);		
		
        return $query->getResult();
    }
    
}

?>