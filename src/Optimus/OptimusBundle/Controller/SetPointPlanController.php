<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Optimus\OptimusBundle\Servicios\ServiceAPTCV;
use Optimus\OptimusBundle\Servicios\ServiceAPAdaptative;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SetPointPlanController extends Controller
{
    public function set_pointAction($idBuilding, $idAPType, $start_date = '')
    {
        $result = array();

        //building
        $result['idBuilding'] = $idBuilding;

        //create an object Datetime of the current date
        $curr_date = new \DateTime();

        /*Set dates*/
        //The start date can either be the current date
        if ($start_date == '') {
            //create the start of the displayed week (starting from curr_date) in the appropriate format
            $result['startDate'] = $curr_date->format("Y-m-d");
            //create the end of the current week (starting from the current date) as object DateTime
            $result['currDate'] = \DateTime::createFromFormat('Y-m-d', $result['startDate'])->modify("+6 day")->format("Y-m-d");
        } //or it can be given via the URL
        else {
            $start_date = new \DateTime($start_date);
            //create the start of the displayed week (starting from start_date) in the appropriate format
            $result['startDate'] = $start_date->format("Y-m-d");
            //create the end of the current week (starting from the current date) as object DateTime
            $result['currDate'] = \DateTime::createFromFormat('Y-m-d', $curr_date->format("Y-m-d"))->modify("+6 day")->format("Y-m-d");
        }

        //create the start of the previous week (starting from startDate - 7 days) in the appropriate format
        $prev_week_start = \DateTime::createFromFormat('Y-m-d', $result['startDate'])->modify("-7 day")->format("Y-m-d");
        //create the end of the current week (startDate + 6 days) in the appropriate format
        $result['endDate'] = \DateTime::createFromFormat('Y-m-d', $result['startDate'])->modify("+6 day")->format("Y-m-d");
		
		$result['nameBuilding']=$this->get('service_data_capturing')->getNameBuilding($idBuilding);
		$result['idAPType']=$idAPType;
		
		// 1. Get info from the Action Plan (DB)
		$em = $this->getDoctrine()->getManager();
		$result['building'] = $em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>$idAPType));
		
		if($actionPlan != null)
		{
			// 2. Get info from data capturing module (config file -> service):
			$idActionPlan=$actionPlan[0]->getId();
			
			$result['idActionPlan']=$idActionPlan;
			$result['dataActionPlan_name'] = $actionPlan[0]->getName();
			$result['dataActionPlan_description'] = $actionPlan[0]->getDescription();
			$result['dataActionPlan_lastCalculation']=$this->get('service_data_capturing')->getLastCalculated($idActionPlan);
			
			set_time_limit(0);
			//Partitions buildings
            $sections = $this->getPartitions($idBuilding);
			
			$calculation=$em->getRepository('OptimusOptimusBundle:APCalculation')->findBy(array('fk_actionplan'=>$actionPlan[0]), array('dateCreation'=>'DESC'));
			$idCalculation=0;
			if(!empty($calculation)) 
			{
				$idCalculation=$calculation[0]->getId();
			}
            

            $result['weekly_proposed_temperatures'] = $this->getDataAdaptive($result['startDate'], $idCalculation);
            $result['partitions'] = $this->getDataTCV($result['startDate'], $idCalculation, $sections);
            $this->APIntegration($result, $sections);
            $this->partitionArray($result['building_sections'], $sections, 0);
            array_shift($result['building_sections']);
			$result['statusDay'] = $this->get('service_apspm_presenter')->getStatusWeek($idActionPlan, $result['startDate'], $result['endDate']);		
		}

        // render the template
        return $this->render('OptimusOptimusBundle:Set_point_tempActionPlan:setpoint_plan.html.twig', $result);
    }
	
	public function newCalculateSetPointManagementAction($idBuilding, $idAPType, $from='', $to='')
	{
		$em = $this->getDoctrine()->getManager();
		$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
		$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
	
		$this->get('service_calculo')->createPredictionAndCalculates($from, $idBuilding, $ip, $user); // <--- !!!!!!! TEMP
		return $this->set_pointAction($idBuilding, $idAPType, $from);
	}

	// Cambia el estado de un dia en el action plan 
	public function changeStatusDayAction(Request $request, $idOutputDay)
	{		
		$em = $this->getDoctrine()->getManager();		
		$outputDay=$em->getRepository('OptimusOptimusBundle:APSPMOutputDay')->findBy(array("id"=>$idOutputDay));
		$elementsForm=array();
		
		if($outputDay)
		{			
			parse_str($request->request->get('data'), $elementsForm);		
			$status=(int)$elementsForm['filter'];
			$outputDay[0]->setStatus($status);
			$em->flush();
			
			//create event status <--- !!!!!!! TEMP		
			$user=$em->getRepository('OptimusOptimusBundle:Users')->find($this->container->get('security.context')->getToken()->getUser()->getId());
			
			if($status==0) 			$newStatus="unknowns";
			elseif($status==1) 		$newStatus="accepts";
			else 					$newStatus="declines";
			
			$textEvent=" ";
			$context="Action plan - Set-Point Management through Thermal Comfort Validation";
			$visible=1;
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$idActionPlan=(int)$elementsForm['idActionPlan'];
			
			$this->get('service_event')->createEvent($user, $textEvent, $context, $idActionPlan, 1, $ip, $request->request->get('idBuilding'), $newStatus);
		}
		
		return new Response("ok");
	}

    private function partitionArray(&$result, $sections, $depth) {
        foreach($sections as $key=>$value) {
            if (is_array($value)) {
                $result[$key] = $depth;
                $this->partitionArray($result, $value, $depth+1);
            }
            else {
                $result[$value] = $depth;
            }
        }
    }

    private function APIntegration (&$result, $sections) {
        foreach($sections as $key=>$value) {
            if (is_array($value)) {
                $this->APIntegration($result, $value);
            }
            else {
                for ($index = 0; $index < 7; $index++) {
                    if ($result['partitions'][$value]['proposed_temperature'][$index] == 0.0) {
                        $result['partitions'][$value]['final_suggestions'][$index] = $result['weekly_proposed_temperatures'][$index]['set_point_temperature'];
                        $result['partitions'][$value]['decision'][$index] = 'Adaptive Comfort';
                    } else {
                        $diff_TCV = abs($result['weekly_proposed_temperatures'][$index]['daily_mean'] - $result['partitions'][$value]['proposed_temperature'][$index]);
                        $diff_adc = abs($result['weekly_proposed_temperatures'][$index]['daily_mean'] - $result['weekly_proposed_temperatures'][$index]['set_point_temperature']);
                        if ($diff_adc > $diff_TCV) {
                            $result['partitions'][$value]['final_suggestions'][$index] = $result['partitions'][$value]['proposed_temperature'][$index];
                            $result['partitions'][$value]['decision'][$index] = 'TCV';
                        } else {
                            $result['partitions'][$value]['final_suggestions'][$index] = $result['weekly_proposed_temperatures'][$index]['set_point_temperature'];
                            $result['partitions'][$value]['decision'][$index] = 'Adaptive Comfort';
                        }
                    }
                }
            }
        }
    }

    private function getPartitions($idBuilding)
    {
        $allPartitions=array();
        $em = $this->getDoctrine()->getEntityManager();
        $partitionsBuilding=$em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
        if($partitionsBuilding) {
            foreach ($partitionsBuilding as $partition) {
                $childs = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_BuildingPartitioning" => $partition->getId()));
                if ($childs)
                    if ($partition->getFkBuildingPartitioning() == NULL) {
                        $allPartitions[$partition->getPartitionName()] = array();
                    }
                    else {
                        $father = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $partition->getFkBuildingPartitioning()));
                        $sections = array();
                        $sections[0] = $father;
                        while ($sections[0]->getFkBuildingPartitioning() != NULL) {
                            $father = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $sections[0]->getFkBuildingPartitioning()));
                            array_unshift($sections, $father);
                        }
                        $partitions = &$allPartitions;
                        for ($i=0; $i<count($sections); $i++) {
                            $partitions = &$partitions[$sections[$i]->getPartitionName()];
                        }
                        $partitions[$partition->getPartitionName()] = array();
                    }
                else {
                    if ($partition->getFkBuildingPartitioning() != NULL) {
                        $father = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $partition->getFkBuildingPartitioning()));
                        $sections = array();
                        $sections[0] = $father;
                        while ($sections[0]->getFkBuildingPartitioning() != NULL) {
                            $father = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $sections[0]->getFkBuildingPartitioning()));
                            array_unshift($sections, $father);
                        }
                        $partitions = &$allPartitions;
                        for ($i=0; $i<count($sections); $i++) {
                            $partitions = &$partitions[$sections[$i]->getPartitionName()];
                        }
                        $partitions[] = $partition->getPartitionName();
                    }
                }
            }
        }
        return $allPartitions;
    }

    private function perPartition(&$data, $Days, $calculation, $sections) {
        foreach($sections as $key=>$value) {
            if(is_array($value)) {
                $this->perPartition($data, $Days, $calculation, $value);
            }
            else {
                $numDays=count($Days);
                for($i=0; $i < $numDays; $i++) {
                    $em = $this->getDoctrine()->getEntityManager();
                    $output = $em->getRepository('OptimusOptimusBundle:APTCVOutput')->findOutputByDate($Days[$i], $calculation, $value);

                    if (!empty($output)) {
                        $data[$value]['proposed_temperature'][$i] = $output[0]->getProposedTemperature();
                        $outputId = $output[0]->getId();
                        $Full_Days = $this->getHoursFromDate($Days[$i]);
                        for ($hour = 0; $hour < 24; $hour++) {
                            $feedback = $em->getRepository('OptimusOptimusBundle:FeedbackOutput')->findOutputByFullDate($Full_Days[$hour], $calculation, $outputId);
                            if (!empty($feedback)) {
                                $data[$value]['feedback'][$i][$hour]['value'] = $feedback[0]->getFeedback();
                                $data[$value]['feedback'][$i][$hour]['size'] = $feedback[0]->getFeedbackSize();
                            }
                            else {
                                $data[$value]['feedback'][$i][$hour]['value'] = 0.0;
                                $data[$value]['feedback'][$i][$hour]['size'] = 0;
                            }
                        }

                    } else {
                        $data[$value]['proposed_temperature'][$i] = '--';
                        for ($hour = 0; $hour < 24; $hour++) {
                            $data[$value]['feedback'][$i][$hour]['value'] = 0.0;
                            $data[$value]['feedback'][$i][$hour]['size'] = 0;
                        }
                    }



                }
            }
        }
    }

    private function getDataTCV($start_date, $calculation, $sections)
    {
        $start_date .= " 00:00:00";
        $actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $start_date);
        $finalDay=$actDay->modify(" +6 day")->format("Y-m-d H:i:s");

        $Days=$this->getDaysFromDate($start_date, $finalDay);
        $data=array();
        $this->perPartition($data, $Days, $calculation, $sections);
        return $data;
    }

    private function getDataAdaptive($start_date, $calculation)
    {
        $start_date .= " 00:00:00";
        $actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $start_date);
        $finalDay=$actDay->modify(" +6 day")->format("Y-m-d H:i:s");

        $Days=$this->getDaysFromDate($start_date, $finalDay);
        $data=array();
        $numDays=count($Days);
        for($i=0; $i < $numDays; $i++) {
            $em = $this->getDoctrine()->getEntityManager();
            $output = $em->getRepository('OptimusOptimusBundle:APAdaptiveOutput')->findOutputByDate($Days[$i], $calculation);

            if (!empty($output)) {
                $date_obj = \DateTime::createFromFormat("Y-m-d H:i:s", $Days[$i]);
                $data[$i] = array('date' => $date_obj->format('D d-m'), 'full_date' => $date_obj->format('Y-m-d'), 'daily_mean' => $output[0]->getDailyMean(), 'set_point_temperature' => $output[0]->getSetPoint());
            } else {
                $date_obj = \DateTime::createFromFormat("Y-m-d H:i:s", $Days[$i]);
                $data[$i] = array('date' => $date_obj->format('D d-m'), 'full_date' => $date_obj->format('Y-m-d'), 'daily_mean' => '--', 'set_point_temperature' => '--');
            }

        }
        return $data;
    }

    private function getHoursFromDate($date) {
        $Days = array();
        $Days[0] = $date;
        for ($i = 1; $i < 24; $i++) {
            $date_obj = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
            $Days[$i] = $date_obj->modify(" +".$i." hour")->format("Y-m-d H:i:s");
        }
        return $Days;
    }

    private function getDaysFromDate($from, $to)
    {
        $aDays=array();
        $aDays[0]=$from;
        for($i=1; $i<7; $i++)
        {
            $actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
            $act=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
            if(($act) < $to)
                $aDays[$i]=$act;
            else break;
        }
        $aDays[$i]=$to;

        return $aDays;
    }

}
?>