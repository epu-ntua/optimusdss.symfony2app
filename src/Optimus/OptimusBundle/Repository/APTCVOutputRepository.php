<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class APTCVOutputRepository extends EntityRepository{

    public function getDQLOutputDate($day, $idCalculation, $section)
    {
        $dqlQuery = 'SELECT aptcvoutput';
        $dqlQuery .= ' FROM '.$this->_entityName.' aptcvoutput';
        $dqlQuery .= " WHERE aptcvoutput.date = '".$day."' and aptcvoutput.fkApCalculation='".$idCalculation."' and aptcvoutput.section='".$section."'";
        return $dqlQuery;

    }

    public function findOutputByDate($day, $idCalculation, $section){

        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLOutputDate($day, $idCalculation, $section);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);

        return $query->getResult();
    }

}