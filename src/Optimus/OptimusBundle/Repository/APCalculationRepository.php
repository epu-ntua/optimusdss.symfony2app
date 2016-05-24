<?php
namespace Optimus\OptimusBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Response;
use OptimusBundle\Entity\APPVOutputDay;
use OptimusBundle\Entity\APCalculation;

class APCalculationRepository extends EntityRepository{

    public function getDQLCalculationDate($day, $idActionPlan)
	{		
		$dqlQuery = 'SELECT apcalculation ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apcalculation ';
        $dqlQuery .= " WHERE '".$day."'>=apcalculation.startingDate and '".$day."'<DATE_ADD(apcalculation.startingDate, 7, 'day') ";
        $dqlQuery .= " and ".$idActionPlan."=apcalculation.fk_actionplan ";
        $dqlQuery .= ' ORDER BY apcalculation.dateCreation DESC ';
		//dump($dqlQuery);		
        return $dqlQuery;		
    }
	
	public function findCalculationByDate($day, $idActionPlan)
	{
		$em = $this->getEntityManager();		
		// IMPORTANT: By default Doctrine does not support all the functions of a specific vendor, such as the DATE_ADD. 
		$dqlQuery = $this->getDQLCalculationDate($day, $idActionPlan);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);
        return $query->getResult();
    }

	public function findAllCalculationsByDate($day, $idActionPlan)
	{
		$em = $this->getEntityManager();		
		// IMPORTANT: By default Doctrine does not support all the functions of a specific vendor, such as the DATE_ADD. 
		$dqlQuery = $this->getDQLCalculationDate($day, $idActionPlan);
        $query = $em->createQuery($dqlQuery);
        return $query->getResult();
    }

	public function getDQLLastCalculationWithPVMOutput($idActionPlan)
	{		
		$dqlQuery = 'SELECT apcalculation ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apcalculation';
        $dqlQuery .= " WHERE '".$idActionPlan."'=apcalculation.fk_actionplan ";
		$dqlQuery .= " AND EXISTS ( SELECT appvmoutput FROM Optimus\OptimusBundle\Entity\APPVMOutput appvmoutput WHERE apcalculation.id = appvmoutput.fkApCalculation ) ";
        $dqlQuery .= ' ORDER BY apcalculation.dateCreation DESC ';
		//dump($dqlQuery);		
        return $dqlQuery;		
    }
	
	public function findLastCalculationWithPVMOutput($idActionPlan)
	{
		$em = $this->getEntityManager();		
		// IMPORTANT: By default Doctrine does not support all the functions of a specific vendor, such as the DATE_ADD. 
		$dqlQuery = $this->getDQLLastCalculationWithPVMOutput($idActionPlan);
		//dump($dqlQuery);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);
        return $query->getResult();
	}
	
	public function getDQLLastCalculationWithESOutput($idActionPlan)
	{		
		$dqlQuery = 'SELECT apcalculation ';
        $dqlQuery .= ' FROM '.$this->_entityName.' apcalculation';
        $dqlQuery .= " WHERE '".$idActionPlan."'=apcalculation.fk_actionplan ";
		$dqlQuery .= " AND EXISTS ( SELECT apflowsoutput FROM Optimus\OptimusBundle\Entity\APFlowsOutput apflowsoutput WHERE apcalculation.id = apflowsoutput.fkApCalculation ) ";
        $dqlQuery .= ' ORDER BY apcalculation.dateCreation DESC ';
		//dump($dqlQuery);		
        return $dqlQuery;		
    }
	
	public function findLastCalculationWithESOutput($idActionPlan)
	{
		$em = $this->getEntityManager();		
		// IMPORTANT: By default Doctrine does not support all the functions of a specific vendor, such as the DATE_ADD. 
		$dqlQuery = $this->getDQLLastCalculationWithESOutput($idActionPlan);
		//dump($dqlQuery);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);
        return $query->getResult();
	}
}

?>