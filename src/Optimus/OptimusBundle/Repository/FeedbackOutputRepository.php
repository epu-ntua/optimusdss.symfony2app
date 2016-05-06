<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class FeedbackOutputRepository extends EntityRepository{

    public function getDQLOutputDate($full_date, $idCalculation, $propositionId)
    {
        $dqlQuery = 'SELECT feedbackoutput';
        $dqlQuery .= ' FROM '.$this->_entityName.' feedbackoutput';
        $dqlQuery .= " WHERE feedbackoutput.full_date = '".$full_date."' and feedbackoutput.fkApCalculation='".$idCalculation."' and feedbackoutput.fkPropositionId='".$propositionId."'";
        return $dqlQuery;

    }

    public function findOutputByFullDate($full_date, $idCalculation, $propositionId){

        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLOutputDate($full_date, $idCalculation, $propositionId);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);

        return $query->getResult();
    }

}