<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APAdaptiveOutputRepository extends EntityRepository{

    public function getDQLOutputDate($day, $idCalculation)
    {
        $dqlQuery = 'SELECT apadaptiveoutput';
        $dqlQuery .= ' FROM '.$this->_entityName.' apadaptiveoutput';
        $dqlQuery .= " WHERE apadaptiveoutput.date = '".$day."' and apadaptiveoutput.fkApCalculation='".$idCalculation."' ";
        return $dqlQuery;

    }

    public function findOutputByDate($day, $idCalculation){

        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLOutputDate($day, $idCalculation);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);

        return $query->getResult();
    }

}