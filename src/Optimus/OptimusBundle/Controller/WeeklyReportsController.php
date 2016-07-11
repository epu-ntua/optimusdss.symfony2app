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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
		
		foreach($data['lastReports'] as $report)
		{		
			if($report->getStatus()==1)			
			{
				//Dates 2016-07-18 / 2016-07-24
				$dateActual=new \DateTime();

				$monday=$report->getMonday();				
				
				$dFromThisWeek = \DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->format("Y-m-d")." 00:00:00";			
				$dTo = \DateTime::createFromFormat('Y-m-d H:i:s', $monday->format("Y-m-d H:i:s"))->modify("+7 days")->format("Y-m-d H:i:s");
				//data: e.consumption & e.cost
				$dataThisWeek=$this->get('service_sensorsRTime')->getRTTime($dTo, $dFromThisWeek, '', $idBuilding);
					
				//User actions
				$userActionsWeek=$em->getRepository('OptimusOptimusBundle:Events')->getUserActionsAPS($idBuilding, $dFromThisWeek, $dTo);		
				
				$report->setEnergyConsumption($dataThisWeek['Energy consumption']);
				$report->setEnergyCost($dataThisWeek['Energy cost']);
				$report->setUserActions($userActionsWeek[0][1]);
				$em->persist($report);
				$em->flush();
			}
		}
				
		//$this->get('service_weeklyReport')->createWeeklyReport();
		
		return $this->render('OptimusOptimusBundle:Reports:tableLastsReports.html.twig', $data);
	}

	public function openFormReportAction($idBuilding, $idWeeklyReport)
	{		
		$data['idBuilding']=$idBuilding;
		$data['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		
		$data['dataForm']=$this->get('service_weeklyReport')->getDataFormWeeklyReport($idBuilding, $idWeeklyReport);	
		
		/*$dir=__DIR__."/../../../../web/bundles/optimus/";
		$data['imgLogo']=realpath($dir)."/img/Logo-Optimus.png";*/
		
		return $this->render('OptimusOptimusBundle:Reports:basicForm.html.twig', $data);
		//return $this->render('OptimusOptimusBundle:Reports:viewPDF.html.twig', $data);
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