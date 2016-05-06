<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class OccupancyConstraintsRepository extends EntityRepository{

    public function getDQLPredictionDate($section, $day)
    {
        $dqlQuery = 'SELECT occupancyconstraints';
        $dqlQuery .= ' FROM '.$this->_entityName.' occupancyconstraints';
        $dqlQuery .= " WHERE occupancyconstraints.date = '".$day."' and  occupancyconstraints.section='".$section."' ";
        return $dqlQuery;
    }

    public function findOutputByDay($section, $day){

        $em = $this->getEntityManager();

        $dqlQuery = $this->getDQLPredictionDate($section, $day);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);

        return $query->getResult();
    }
}

?>