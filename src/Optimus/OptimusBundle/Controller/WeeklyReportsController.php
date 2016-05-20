<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Optimus\OptimusBundle\Entity\WeeklyReport;
use Optimus\OptimusBundle\Entity\WeeklyReportActionPlan;
use Optimus\OptimusBundle\Entity\WeeklyReportUsers;
use Optimus\OptimusBundle\Entity\EvaluationCriteria;

class WeeklyReportsController extends Controller
{
	public function lastsReportsAction ($idBuilding, $idActionPlan=null)
	{
		$em = $this->getDoctrine()->getManager();
		$data['lastReports']=$em->getRepository('OptimusOptimusBundle:WeeklyReport')->findBy(array("fk_Building"=>$idBuilding), array('datetime'=>'DESC'), 3);
		
		$data['idBuilding']=$idBuilding;
		
		return $this->render('OptimusOptimusBundle:Reports:lastsReports.html.twig', $data);
	}
	
	public function tableReportsAction($idBuilding, $insertDB=false)
	{
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		$em = $this->getDoctrine()->getManager();
		$building=$em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
						
		$data['lastReports']=$em->getRepository('OptimusOptimusBundle:WeeklyReport')->findBy(array("fk_Building"=>$idBuilding), array('datetime'=>'DESC'));
		
		//check report status 0 pdf : if no exist file --> create
		
		return $this->render('OptimusOptimusBundle:Reports:tableLastsReports.html.twig', $data);
	}

	public function openFormReportAction($idBuilding, $idWeeklyReport)
	{
		$em = $this->getDoctrine()->getManager();
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		//get all weekly report
		$data['weeklyReport']=$em->getRepository('OptimusOptimusBundle:WeeklyReport')->find($idWeeklyReport);
		
		//Usuarios del weekly report
		$data['usersInWR']=$em->getRepository('OptimusOptimusBundle:WeeklyReportUsers')->findBy(array("fk_weeklyreport"=>$idWeeklyReport));
		
		//Get EvaluationCriteria
		$data['evCriteria']=$em->getRepository('OptimusOptimusBundle:EvaluationCriteria')->findBy(array("fk_weeklyreport"=>$idWeeklyReport));
		
		//Dates
		
		$monday=$data['weeklyReport']->getMonday();
		//dump($monday);
		$startDate=\DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("Monday this week")->format("Y-m-d");
		$endDate=\DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("Saturday this week")->format("Y-m-d");
		$data['sundayDate']=\DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("Sunday this week")->format("Y-m-d");
		$data['startDate']=$startDate;
		
		//actionsPlans
		$data['allActionsPlans']=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "status"=>1));
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
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_apo_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);					
					break;
					
					case 2:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_apspm_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 3:						
					break;
					
					case 4:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_apph_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 5:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_appvm_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 6:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_appv_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
					
					case 7:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_apsource_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;case 8:
						$data['statusWeekActionPlan'][$actionPlan->getId()]=$this->get('service_apeconomizer_presenter')->getStatusWeek($actionPlan->getId(), $startDate, $endDate);
					break;
				}
				//getData weeklyReportActionsPlans
				$data['weeklyReportActionPlan'][$actionPlan->getId()]=$em->getRepository('OptimusOptimusBundle:WeeklyReportActionPlan')->findBy(array("fk_weeklyreport"=>$idWeeklyReport, "fk_actionplan"=>$actionPlan->getId()));
			}		
		}else{
			$data['weeklyReportActionPlan']='';
			$data['statusWeekActionPlan']='';
		}	
		
		// Test de PDF
		/*$this->get('knp_snappy.pdf')->generateFromHtml(
			$this->renderView(
				'OptimusOptimusBundle:Reports:basicForm.html.twig',	$data),
			'C:\xampp\htdocs\optimus\web\bundles\optimus\pdf\testPDF_1.pdf');*/
		
		//View
		//dump($data);
		return $this->render('OptimusOptimusBundle:Reports:basicForm.html.twig', $data);
	}
	
	public function saveDataFormReportAction($idBuilding, $idWeeklyReport, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		//weeklyReport edit
		$weeklyReport=$em->getRepository('OptimusOptimusBundle:WeeklyReport')->find($idWeeklyReport);
				
		$weeklyReport->setExperienceDifficulties($request->request->get('difficulties'));
		$weeklyReport->setExperienceCalibration($request->request->get('calibration'));
		$weeklyReport->setExperienceEvents($request->request->get('events'));
		$em->persist($weeklyReport);
		$em->flush();
		
		//users edit form
		$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
		
		$usersInWR=$em->getRepository('OptimusOptimusBundle:WeeklyReportUsers')->findBy(array("fk_weeklyreport"=>$idWeeklyReport, "fk_user"=>$user->getId()));
		if(!$usersInWR)
		{		
			$weeklyReportUser= new WeeklyReportUsers();
			$weeklyReportUser->setFkWeeklyreport($weeklyReport);
			$weeklyReportUser->setFkUser($user);
			
			$em->persist($weeklyReportUser);
			$em->flush();	
		}
		
		//Edit weeklyReportActionPlan
		//actionsPlans
		$data['allActionsPlans']=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "status"=>1));
		if($data['allActionsPlans'])
		{
			foreach($data['allActionsPlans'] as $actionPlan)
			{
				$nameText='text_'.$actionPlan->getId();
				$nameLesson='lesson_'.$actionPlan->getId();
				
				$weeklyReportActionPlan=$em->getRepository('OptimusOptimusBundle:WeeklyReportActionPlan')->findBy(array("fk_weeklyreport"=>$idWeeklyReport, "fk_actionplan"=>$actionPlan->getId()));
				if($weeklyReportActionPlan[0])
				{
					$weeklyReportActionPlan[0]->setTextProcedure($request->request->get($nameText));
					$weeklyReportActionPlan[0]->setLessonLearned($request->request->get($nameLesson));
					$em->persist($weeklyReportActionPlan[0]);
					$em->flush();
				}
			}
		}		
		
		//Edit evaluation criteria
		$evaluationCriteria=$em->getRepository('OptimusOptimusBundle:EvaluationCriteria')->findBy(array("fk_weeklyreport"=>$idWeeklyReport));
				
		$evaluationCriteria[0]->setScore1($request->request->get('score1'));
		$evaluationCriteria[0]->setText1($request->request->get('text1'));
		$evaluationCriteria[0]->setScore2($request->request->get('score2'));
		$evaluationCriteria[0]->setText2($request->request->get('text2'));
		$evaluationCriteria[0]->setScore3($request->request->get('score3'));
		$evaluationCriteria[0]->setText3($request->request->get('text3'));
		$evaluationCriteria[0]->setScore4($request->request->get('score4'));
		$evaluationCriteria[0]->setText4($request->request->get('text4'));
		$evaluationCriteria[0]->setScore5($request->request->get('score5'));
		$evaluationCriteria[0]->setText5($request->request->get('text5'));
		$evaluationCriteria[0]->setScore6($request->request->get('score6'));
		$evaluationCriteria[0]->setText6($request->request->get('text6'));
		$evaluationCriteria[0]->setScore7($request->request->get('score7'));
		$evaluationCriteria[0]->setText7($request->request->get('text7'));
		$em->persist($evaluationCriteria[0]);
		$em->flush();
				
		return $this->redirect($this->generateUrl('tableReports', array('idBuilding'=>$idBuilding, 'insertDB'=>true)));
	}
}

?>