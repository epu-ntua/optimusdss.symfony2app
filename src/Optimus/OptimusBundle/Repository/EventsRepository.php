<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class EventsRepository extends EntityRepository{

    public function getDQLLastsEvents($idBuilding)
	{		
		$dqlQuery = 'SELECT events ';
        $dqlQuery .= ' FROM '.$this->_entityName.' events';
        $dqlQuery .= ' WHERE events.visible=1 and events.fk_Building='.$idBuilding.'';
        $dqlQuery .= ' ORDER BY events.date DESC';
        return $dqlQuery;	
		
    }
	
	public function getDQLLastsEventsActionPlan($idActionPlan)
	{		
		$dqlQuery = 'SELECT events ';
        $dqlQuery .= ' FROM '.$this->_entityName.' events';
        $dqlQuery .= ' WHERE events.visible=1 and events.id_context='.$idActionPlan.'';
        $dqlQuery .= ' ORDER BY events.date DESC';
        return $dqlQuery;	
		
    }
	
	public function findLastEvents($idBuilding, $idActionPlan){
      
        $em = $this->getEntityManager();
        if($idActionPlan==null)
			$dqlQuery = $this->getDQLLastsEvents($idBuilding);
		else
			$dqlQuery = $this->getDQLLastsEventsActionPlan($idActionPlan);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(10);		
		
        return $query->getResult();
    }
	
	public function getDQLUserActionsAPS($idBuilding, $startDate, $finalDate)
	{
		$dqlQuery = 'SELECT count(events) ';
        $dqlQuery .= ' FROM '.$this->_entityName.' events';
        $dqlQuery .= ' WHERE events.fk_Building='.$idBuilding.'';
        $dqlQuery .= " AND (events.action='accepts' or events.action='declines' or events.action='unknowns')";
        $dqlQuery .= " AND events.date >='".$startDate."' AND events.date <='".$finalDate."' ";
   		
        return $dqlQuery;
	}
	
	public function getUserActionsAPS($idBuilding, $startDate, $finalDate)
	{
		$em = $this->getEntityManager();
		$dqlQuery = $this->getDQLUserActionsAPS($idBuilding, $startDate, $finalDate);
		$query = $em->createQuery($dqlQuery);
		
		return $query->getResult();
	}
    
}

?>