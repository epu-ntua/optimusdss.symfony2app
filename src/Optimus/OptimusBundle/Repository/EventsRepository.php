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
    
}

?>