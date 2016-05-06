<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APSwitchOutputRepository extends EntityRepository{

    public function getDQLOutputsDate($calculation, $dayStart, $dayEnd)
	{		
		$dqlQuery = 'SELECT apswitchoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apswitchoutput';
        $dqlQuery .= " WHERE apswitchoutput.day >= '".$dayStart."' and  apswitchoutput.day <='".$dayEnd."' ";
        $dqlQuery .= " and apswitchoutput.fkApCalculation = ".$calculation." ";
        
		//dump($dqlQuery);
		
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