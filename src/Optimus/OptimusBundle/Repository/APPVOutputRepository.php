<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APPVOutputRepository extends EntityRepository{

    public function getDQLOutputsDate($calculation, $dayStart, $dayEnd)
	{		
		$dqlQuery = 'SELECT appvoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvoutput';
        $dqlQuery .= " WHERE appvoutput.hour >= '".$dayStart."' and  appvoutput.hour<='".$dayEnd."' ";
        $dqlQuery .= " and appvoutput.fkApCalculation = ".$calculation." ";
        $dqlQuery .= ' ORDER BY appvoutput.hour ASC';
        return $dqlQuery;	
    }
	
	public function findResgisterOutputsByDate($calculation, $day){
      
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