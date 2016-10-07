<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\WeeklyReport;
use Optimus\OptimusBundle\Entity\WeeklyReportActionPlan;
use Optimus\OptimusBundle\Entity\WeeklyReportUsers;
use Optimus\OptimusBundle\Entity\EvaluationCriteria;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Symfony\Component\Templating\EngineInterface;

class ServiceWeeklyReport {
	 
    protected $em;	
    protected $apo;	
    protected $apspm;	
    protected $aph;	
    protected $appvm;	
    protected $appv;	
    protected $apsource;	
    protected $apeconomizer;
	protected $rtime;
	
	private $templating;
	private $knpSnappyPdf;
	private $mailer;
	
	protected $mailer_from;
	protected $mailer_to;
	
	private $dir= __DIR__."/../../../../web/bundles/optimus/";
	
    public function __construct(EntityManager $em,
								ServiceAPOPresenter $apo,
								ServiceAPSPMPresenter $apspm,
								ServiceAPPHPresenter $aph,
								ServiceAPPVMPresenter $appvm,
								ServiceAPPVPresenter $appv,
								ServiceAPEnergySourcePresenter $apsource,
								ServiceAPEconomizerPresenter $apeconomizer,
								ServiceBuildingSensorsRTime $rtime, 
								EngineInterface $templating,
								LoggableGenerator $knpSnappyPdf,
								\Swift_Mailer $mailer,
								$mailer_from,
								$mailer_to
								)
    {
		$this->em=$em;		
		$this->apo=$apo;		
		$this->apspm=$apspm;		
		$this->aph=$aph;		
		$this->appvm=$appvm;		
		$this->appv=$appv;		
		$this->apsource=$apsource;		
		$this->apeconomizer=$apeconomizer;
		$this->rtime = $rtime;
		$this->templating   = $templating;
		$this->knpSnappyPdf = $knpSnappyPdf;
		$this->mailer = $mailer;
		$this->mailer_from = $mailer_from;
		$this->mailer_to = $mailer_to;
    }
	
	//this method updates the figures of all weekly reports
	public function statisticsReports() 
	{
		$buildings=$this->em->getRepository('OptimusOptimusBundle:Building')->findAll();
		$startDate = "2016-01-01";
		$endDate = "2016-10-07";
		echo " -- Collecting statistics for buildings from ".$startDate." to ".$endDate."\n";
		
		foreach($buildings as $building)
		{			
			
			//actionsPlans
			$data['allActionsPlans']=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$building->getId()));
			if($data['allActionsPlans'])
			{	
				$data['weeklyReportActionPlan']=array();
				$data['statusWeekActionPlan']=array();		
				
				foreach($data['allActionsPlans'] as $actionPlan)
				{
					$ap = array();
					//Get fk_calculation según dates
					switch ($actionPlan->getType())
					{
						case 1:	$ap = $this->apo->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
						case 2: $ap = $this->apspm->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
						case 3:	break;
						case 4: $ap = $this->aph->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
						case 5: $ap = $this->appvm->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
						case 6: $ap = $this->appv->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
						case 7: $ap = $this->apsource->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
						case 8: $ap = $this->apeconomizer->getStatusWeek($actionPlan->getId(), $startDate, $endDate); break;
					}

					//echo "sadas: ".$actionPlan->getType(). "\n";
					//var_dump($ap);
					
					$accept = 0;
					$decline = 0;
					foreach($ap as $actionPlanDay) {
						if($actionPlanDay["status"] == 1) {  		// accept
							$accept ++;
						} else if($actionPlanDay["status"] == 2) {	// decline
							$decline ++;
						} 
					}
					
					echo $building->getName() . ";" . $actionPlan->getName() . ";" . $accept . ";" .$decline."\n";
				}	
		
			}
			
		}
	}
	
	
	//this method updates the figures of all weekly reports
	public function updateWeeklyReports() 
	{
		$buildings=$this->em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		foreach($buildings as $building)
		{
			echo " -- Updating building: ".$building->getName()." -- \n";
			
			$weeklyReportsBuilding=$this->em->getRepository('OptimusOptimusBundle:WeeklyReport')->findBy(array("fk_Building"=>$building->getId()), array("datetime"=>'DESC'));
			
			for($i=0; $i < count($weeklyReportsBuilding); $i++) {
				$monday=$weeklyReportsBuilding[$i]->getMonday();				
				
				$dFromThisWeek = \DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->format("Y-m-d")." 00:00:00";			
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("+7 days")->format("Y-m-d H:i:s");
				
				echo " -- report from: ".$dFromThisWeek." to: ".$dTo." -- \n";
				
				//data: e.consumption & e.cost
				$dataThisWeek=$this->rtime->getRTTime($dTo, $dFromThisWeek, '', $building->getId());
					
				//User actions
				$dataFormWeeklyReport = $this->getDataFormWeeklyReport($building->getId(), $weeklyReportsBuilding[$i]->getId());	
				
				$userActionsWeek = $this->getNumberOfActions($dataFormWeeklyReport);
				
				//$userActionsWeek=$this->em->getRepository('OptimusOptimusBundle:Events')->getUserActionsAPS($building->getId(), $dFromThisWeek, $dTo);		
				
				$weeklyReportsBuilding[$i]->setEnergyConsumption($dataThisWeek['Energy consumption']);
				$weeklyReportsBuilding[$i]->setEnergyCost($dataThisWeek['Energy cost']);
				//$weeklyReportsBuilding[$i]->setUserActions($userActionsWeek[0][1]);
				$weeklyReportsBuilding[$i]->setUserActions($userActionsWeek);
				
				dump($weeklyReportsBuilding[$i]);
				
				$this->em->persist($weeklyReportsBuilding[$i]);
				$this->em->flush();
				
				$this->getPDFWRAction($building, $weeklyReportsBuilding[$i]->getId(), $weeklyReportsBuilding[$i]->getPeriod(), $dataFormWeeklyReport, false);
			}
		}
	}
	
	//this method creates a new weekly report and closes the previous one
	public function createWeeklyReport()
	{
		$buildings=$this->em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		foreach($buildings as $building)
		{
			echo " -- Building: ".$building->getName()." -- \n";
			
			$dateActual=new \DateTime();			
			$monday =\DateTime::createFromFormat('Y-m-d', $dateActual->format("Y-m-d"))->modify("Monday next week");
			
			//$nextMonday = \DateTime::createFromFormat('Y-m-d', $dateActual->format("Y-m-d"))->modify("Monday next week")->format("Y-m-d");
            $nextMonday = \DateTime::createFromFormat('Y-m-d', $dateActual->format("Y-m-d"))->modify("Monday next week");
			$nextSunday = \DateTime::createFromFormat('Y-m-d', $nextMonday->format("Y-m-d"))->modify("Sunday this week")->format("Y-m-d");
			
			$period=$nextMonday->format("Y-m-d")." / ".$nextSunday;
			
			//Get last weeklyReport building and set Status=0;			
			$lastWeeklyReportBuilding=$this->em->getRepository('OptimusOptimusBundle:WeeklyReport')->findBy(array("fk_Building"=>$building->getId()), array("datetime"=>'DESC'));
			if(isset($lastWeeklyReportBuilding[0])) 
			{
				
				echo "set status 0 last weekly report \n";
				
				$monday=$lastWeeklyReportBuilding[0]->getMonday();				
				
				$dFromThisWeek = \DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->format("Y-m-d")." 00:00:00";			
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("+7 days")->format("Y-m-d H:i:s");
				//data: e.consumption & e.cost
				$dataThisWeek=$this->rtime->getRTTime($dTo, $dFromThisWeek, '', $building->getId());
					
				//User actions
				$dataFormWeeklyReport = $this->getDataFormWeeklyReport($building->getId(), $lastWeeklyReportBuilding[0]->getId());	

				$userActionsWeek = $this->getNumberOfActions($dataFormWeeklyReport);
				//$userActionsWeek=$this->em->getRepository('OptimusOptimusBundle:Events')->getUserActionsAPS($building->getId(), $dFromThisWeek, $dTo);		
				
				$lastWeeklyReportBuilding[0]->setEnergyConsumption($dataThisWeek['Energy consumption']);
				$lastWeeklyReportBuilding[0]->setEnergyCost($dataThisWeek['Energy cost']);
				//$lastWeeklyReportBuilding[0]->setUserActions($userActionsWeek[0][1]);
				$lastWeeklyReportBuilding[0]->setUserActions($userActionsWeek);
				
				$lastWeeklyReportBuilding[0]->setStatus(0);//$status
				$this->em->persist($lastWeeklyReportBuilding[0]);
				$this->em->flush();
				//dump($lastWeeklyReportBuilding[0]);
				//create PDF
				$this->getPDFWRAction($building, $lastWeeklyReportBuilding[0]->getId(), $lastWeeklyReportBuilding[0]->getPeriod(), $dataFormWeeklyReport, true);
			}
		
			
			//Insert a new weekly report
			$weeklyReport = new WeeklyReport();
			$weeklyReport->setPeriod($period);//$period
			$weeklyReport->setEnergyConsumption(0);//$energyConsumption
			$weeklyReport->setEnergyCost(0);//$energyCost
			$weeklyReport->setUserActions(0);//$userActions
			$weeklyReport->setMonday($nextMonday);//$datetime
			$weeklyReport->setDatetime($dateActual);//$datetime
			$weeklyReport->setStatus(1);//$status
			$weeklyReport->setExperienceDifficulties('');//$experienceDifficulties
			$weeklyReport->setExperienceCalibration('');//$experienceCalibration
			$weeklyReport->setExperienceEvents('');//$experienceEvents		
			$weeklyReport->setFkBuilding($building);
			
			$this->em->persist($weeklyReport);
			$this->em->flush();	
			
			echo "new WeeklyReport \n";
		
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
				
				echo "new WeeklyReportActionPlan".$actionPlan->getId()." \n";
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
			
			echo "new EvaluationCriteria \n";
		}
	}

	public function getNumberOfActions ($data) {
		$actions = 0;
		
		foreach($data['statusWeekActionPlan'] as $actionPlan) {
			
			foreach($actionPlan as $actionPlanDay) {
				if($actionPlanDay["status"] > 0)
					$actions++;
				//echo " - getNumberOfActions: " . $actionPlanDay["status"];
			}
		}
		
		return $actions;
	}
	
	
	public function getDataFormWeeklyReport($idBuilding, $idWeeklyReport)
	{		
		//get all weekly report
		$data['weeklyReport']=$this->em->getRepository('OptimusOptimusBundle:WeeklyReport')->find($idWeeklyReport);
		
		//Usuarios del weekly report
		$data['usersInWR']=$this->em->getRepository('OptimusOptimusBundle:WeeklyReportUsers')->findBy(array("fk_weeklyreport"=>$idWeeklyReport));
		
		//Get EvaluationCriteria
		$data['evCriteria']=$this->em->getRepository('OptimusOptimusBundle:EvaluationCriteria')->findBy(array("fk_weeklyreport"=>$idWeeklyReport));
		
		//Dates		
		$monday=$data['weeklyReport']->getMonday();		
		$startDate=\DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("Monday this week")->format("Y-m-d");
		$endDate=\DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("Saturday this week")->format("Y-m-d");
		$data['sundayDate']=\DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("Sunday this week")->format("Y-m-d");
		$data['startDate']=$startDate;
		
		//actionsPlans
		$data['allActionsPlans']=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "status"=>1));
		if($data['allActionsPlans'])
		{	
			$data['weeklyReportActionPlan']=array();
			$data['statusWeekActionPlan']=array();		
			foreach($data['allActionsPlans'] as $actionPlan)
			{
				//Get fk_calculation según dates
				switch ($actionPlan->getType())
				{
					case 1:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->apo->getStatusWeek($actionPlan->getId(), $startDate, $endDate);					
					break;
					
					case 2:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->apspm->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 3:						
					break;
					
					case 4:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->aph->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 5:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->appvm->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 6:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->appv->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 7:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->apsource->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;case 8:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->apeconomizer->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
				}
				//getData weeklyReportActionsPlans
				$data['weeklyReportActionPlan'][$actionPlan->getId()]=$this->em->getRepository('OptimusOptimusBundle:WeeklyReportActionPlan')->findBy(array("fk_weeklyreport"=>$idWeeklyReport, "fk_actionplan"=>$actionPlan->getId()));
			}		
		}else{
			$data['weeklyReportActionPlan']='';
			$data['statusWeekActionPlan']='';
		}
		
		return $data;
	}

	public function getPDFWRAction($building, $idWeeklyReport, $period, $dataFormWeeklyReport, $email)
	{		
		$weeklyReport=$this->em->getRepository('OptimusOptimusBundle:WeeklyReport')->find($idWeeklyReport);
		if($weeklyReport)
		{			
			if($weeklyReport->getStatus()==0)
			{
				//check if exist pdf
				$fs = new Filesystem();
				
				$nameFile=realpath($this->dir)."/pdf/report_".$idWeeklyReport.".pdf";
				if($fs->exists($nameFile)==false)	
				{
					//Create PDF					
					$data['idBuilding']=$building->getId();
					$data['nameBuilding']=$building->getName();
					$data['dataForm']=$dataFormWeeklyReport;
					$data['imgLogo']=realpath($this->dir)."/img/Logo-Optimus.png";
					
					$this->knpSnappyPdf->generateFromHtml(
						$this->templating->render(
							'OptimusOptimusBundle:Reports:viewPDF.html.twig',	$data),
						$nameFile);
					
					if($email) {
						$toEmails=explode(",", $this->mailer_to);
						
						//Send mail with pdf create
						$message = \Swift_Message::newInstance()
							->setSubject('OPTIMUSDSS - '.$period.' - ['.$idWeeklyReport.'] Weekly Report for ' . $building->getName() . ', ' . $building->getCity())
							->setFrom($this->mailer_from)
							->setCc($toEmails)
							->setContentType("text/html")
							->setBody('OPTIMUSDSS - '.$period.' - ['.$idWeeklyReport.'] Weekly Report for ' . $building->getName() . ', ' . $building->getCity())
							->attach(\Swift_Attachment::fromPath($nameFile));
									
						$this->mailer->send($message);
					}					
				}
			}
		}
	}
}

?>