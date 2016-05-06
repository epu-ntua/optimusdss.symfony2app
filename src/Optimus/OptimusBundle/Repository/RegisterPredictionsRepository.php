<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class RegisterPredictionsRepository extends EntityRepository{

    public function getDQLPredictionDate($sensor_id, $prediction, $dayStart, $dayEnd)
	{		
		$dqlQuery = 'SELECT registerpredictions ';
        $dqlQuery .= ' FROM '.$this->_entityName.' registerpredictions';
        $dqlQuery .= " WHERE registerpredictions.date >= '".$dayStart."' and  registerpredictions.date<='".$dayEnd."' ";
        $dqlQuery .= " and registerpredictions.fk_sensor = '".$sensor_id."' and  registerpredictions.fk_prediction='".$prediction."' ";
        $dqlQuery .= ' ORDER BY registerpredictions.date ASC';
        return $dqlQuery;	
    }
	
	public function findResgisterPredictionsByDate($sensor_id, $prediction, $day){
      
        $em = $this->getEntityManager();
		$date=explode(" ", $day);
		$dayStart=$date[0]." 00:00:00";
		$dayEnd=$date[0]." 23:59:00";
		
        $dqlQuery = $this->getDQLPredictionDate($sensor_id, $prediction, $dayStart, $dayEnd);
        $query = $em->createQuery($dqlQuery);
			//$query->setMaxResults(1);		
		
        return $query->getResult();
    }
    
}

?>