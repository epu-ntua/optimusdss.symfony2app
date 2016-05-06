<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APPVMOutputRepository extends EntityRepository{

    public function getDQLOutputsDate($calculation, $dayStart, $dayEnd)
	{		
		$dqlQuery = 'SELECT appvmoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvmoutput';
        $dqlQuery .= " WHERE appvmoutput.hour >= '".$dayStart."' and  appvmoutput.hour<='".$dayEnd."' ";
        $dqlQuery .= " and appvmoutput.fkApCalculation = ".$calculation." ";
        $dqlQuery .= ' ORDER BY appvmoutput.hour ASC';
        return $dqlQuery;	
    }
	
	public function findResgisterOutputsByDate($calculation, $day){
      
        $em = $this->getEntityManager();
		$date=explode(" ", $day);
		$dayStart=$day;
		$dayEnd=$date[0]." 23:59:00";
		
        $dqlQuery = $this->getDQLOutputsDate($calculation, $dayStart, $dayEnd);
		//dump($dqlQuery);
        $query = $em->createQuery($dqlQuery);
			//$query->setMaxResults(1);		
		
        return $query->getResult();
    }
	
	public function getDQLLastOutputs($calculation)
	{		
		$dqlQuery = 'SELECT appvmoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvmoutput ';
        $dqlQuery .= " WHERE appvmoutput.fkApCalculation = ".$calculation." ";
        $dqlQuery .= ' ORDER BY appvmoutput.hour DESC ';
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
	
	public function getDQLOutputsByDate($idActionPlan, $dayStart, $dayEnd)
	{		
		$dqlQuery  = ' SELECT appvmoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' appvmoutput ';
		$dqlQuery .= ' INNER JOIN Optimus\OptimusBundle\Entity\APCalculation apcalculation WITH apcalculation.id = appvmoutput.fkApCalculation ';
        $dqlQuery .= " WHERE appvmoutput.hour >= '".$dayStart."' AND  appvmoutput.hour<='".$dayEnd."' ";
		$dqlQuery .= " AND '".$idActionPlan."'=apcalculation.fk_actionplan ";
        $dqlQuery .= ' ORDER BY appvmoutput.hour ASC';
		
        return $dqlQuery;	
    }
	
	public function findOutputsByDate($idActionPlan, $day){
      
        $em = $this->getEntityManager();
		$date=explode(" ", $day);
		$dayStart=$day;
		$dayEnd=$date[0]." 23:59:00";
		
        $dqlQuery = $this->getDQLOutputsByDate($idActionPlan, $dayStart, $dayEnd);
		//dump($dqlQuery);
        $query = $em->createQuery($dqlQuery);
		
        return $query->getResult();
    }
	
	
	
    
}

?>