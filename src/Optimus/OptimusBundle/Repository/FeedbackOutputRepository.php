<?php
namespace Optimus\OptimusBundle\Repository;
use Doctrine\ORM\EntityRepository;

class FeedbackOutputRepository extends EntityRepository{

    public function getDQLOutputDate($full_date, $section)
    {
        $dqlQuery = 'SELECT feedbackoutput';
        $dqlQuery .= ' FROM '.$this->_entityName.' feedbackoutput';
        $dqlQuery .= " WHERE feedbackoutput.full_date = '".$full_date."' and feedbackoutput.section='".$section."'";
        return $dqlQuery;

    }

    public function findOutputByFullDate($full_date, $section){

        $em = $this->getEntityManager();
        $dqlQuery = $this->getDQLOutputDate($full_date, $section);
        $query = $em->createQuery($dqlQuery);
        $query->setMaxResults(1);

        return $query->getResult();
    }

}