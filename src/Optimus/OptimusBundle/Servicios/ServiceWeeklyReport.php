<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\WeeklyReport;
use Optimus\OptimusBundle\Entity\WeeklyReportActionPlan;
use Optimus\OptimusBundle\Entity\WeeklyReportUsers;
use Optimus\OptimusBundle\Entity\EvaluationCriteria;

class ServiceWeeklyReport {
	 
    protected $em;	
	
    public function __construct(EntityManager $em)
    {
		$this->em=$em;		
    }
	
	public function createWeeklyReport()
	{
		$buildings=$this->em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		foreach($buildings as $building)
		{	
			$dateActual=new \DateTime();			
			$monday =\DateTime::createFromFormat('Y-m-d', $dateActual->format("Y-m-d"))->modify("Monday next week");
			
			$nextMonday = \DateTime::createFromFormat('Y-m-d', $dateActual->format("Y-m-d"))->modify("Monday next week")->format("Y-m-d");
			$nextSunday = \DateTime::createFromFormat('Y-m-d', $nextMonday)->modify("Sunday this week")->format("Y-m-d");
			
			$period=$nextMonday." / ".$nextSunday;
			
			//Get last weeklyReport building and set Status=0;			
			$lastWeeklyReportBuilding=$this->em->getRepository('OptimusOptimusBundle:WeeklyReport')->findBy(array("fk_Building"=>$building->getId()), array("datetime"=>'DESC'));
			if(isset($lastWeeklyReportBuilding[0])) 
			{
				echo "set status last weekly report";
				$lastWeeklyReportBuilding[0]->setStatus(0);//$status
				$this->em->persist($lastWeeklyReportBuilding[0]);
				$this->em->flush();
			}
		
			//Insert a new weekly report
			$weeklyReport = new WeeklyReport();
			$weeklyReport->setPeriod($period);//$period
			$weeklyReport->setEnergyConsumption(0);//$energyConsumption
			$weeklyReport->setEnergyCost(0);//$energyCost
			$weeklyReport->setUserActions(0);//$userActions
			$weeklyReport->setMonday($monday);//$datetime
			$weeklyReport->setDatetime($dateActual);//$datetime
			$weeklyReport->setStatus(1);//$status
			$weeklyReport->setExperienceDifficulties('');//$experienceDifficulties
			$weeklyReport->setExperienceCalibration('');//$experienceCalibration
			$weeklyReport->setExperienceEvents('');//$experienceEvents		
			$weeklyReport->setFkBuilding($building);
			
			$this->em->persist($weeklyReport);
			$this->em->flush();	
			
			echo "new WeeklyReport";
		
			//Insert weekly report - action plan
			$allActionPlans=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$building->getId(), "status"=>1));
		
			foreach($allActionPlans as $actionPlan)
			{
				//insert a new weekly report - action plan
				$weeklyReportAP = new WeeklyReportActionPlan();
				$weeklyReportAP->setTextProcedure('');//$textProcedure
				$weeklyReportAP->setLessonLearned('');//$lessonLearned
				$weeklyReportAP->setFkWeeklyreport($weeklyReport);
				$weeklyReportAP->setFkActionplan($actionPlan);
				
				$this->em->persist($weeklyReportAP);
				$this->em->flush();	
				
				echo "new WeeklyReportActionPlan".$actionPlan->getId();
			}
			
			//insert a new weekly report - evaluation criteria
			$evCriteria= new EvaluationCriteria();
			$evCriteria->setFkWeeklyreport($weeklyReport);					
			$evCriteria->setScore1(1);
			$evCriteria->setText1("");
			$evCriteria->setScore2(1);
			$evCriteria->setText2("");
			$evCriteria->setScore3(1);
			$evCriteria->setText3("");
			$evCriteria->setScore4(1);
			$evCriteria->setText4("");
			$evCriteria->setScore5(1);
			$evCriteria->setText5("");
			$evCriteria->setScore6(1);
			$evCriteria->setText6("");
			$evCriteria->setScore7(1);
			$evCriteria->setText7("");
			
			$this->em->persist($evCriteria);
			$this->em->flush();	
			
			echo "new EvaluationCriteria";
		}
	}
}

?>