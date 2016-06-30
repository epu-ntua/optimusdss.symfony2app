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
	
	
	public function getDQLOutputDay($dayStart, $dayEnd, $section)
	{		
		$dqlQuery = 'SELECT feedbackoutput ';
        $dqlQuery .= ' FROM '.$this->_entityName.' feedbackoutput';
        $dqlQuery .= " WHERE feedbackoutput.full_date >= '".$dayStart."' and  feedbackoutput.full_date<='".$dayEnd."' ";
        $dqlQuery .= " and feedbackoutput.section = '".$section."' ";
        $dqlQuery .= ' ORDER BY feedbackoutput.full_date ASC';
        return $dqlQuery;	
    }

    public function findOutputByDay($day, $section){

        $em = $this->getEntityManager();
		$date=explode(" ", $day);
		$dayStart=$day;
		$dayEnd=$date[0]." 23:59:00";
		
        $dqlQuery = $this->getDQLOutputDay($dayStart, $dayEnd, $section);
        $query = $em->createQuery($dqlQuery);
		
        return $query->getResult();
    }

}