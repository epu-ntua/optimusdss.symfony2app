<?php

namespace Optimus\OptimusBundle\Controller;

use Optimus\OptimusBundle\Entity\OccupancyConstraints;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\FileLocator;


class OccupancyController extends Controller
{
    public function occupancyAction($idBuilding, $idAPType, $start_date = '', $constraints = '')
    {
        $result = array();

        //building
        $result['idBuilding'] = $idBuilding;

        if ($constraints != '') {
            $constraints = explode(';', $constraints);
        }

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

        $result['dates']=$this->getDaysFromDate($result['startDate']);

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

            //$calculation=$em->getRepository('OptimusOptimusBundle:APCalculation')->findBy(array('fk_actionplan'=>$actionPlan[0]), array('dateCreation'=>'DESC'));
            //$idCalculation=$calculation[0]->getId();

            $result['partitions'] = array();
            $ordered = array();

            $Days = $this->getDaysFromDate($result['startDate']);
            //Partitions buildings
            $sections = $this->getPartitionConstraints($result['partitions'], $ordered, $idBuilding, $result['startDate'], $Days, $constraints);

            $result['estimated_occupancy'] = $this->getRandOccupancy($Days);

            $this->getSuggestions($result['partitions'], $ordered, $result['estimated_occupancy'], $Days, $sections, $idBuilding);

            $result['building_sections'] = $this->partitionArray($sections);
        }
		

		/* Get Status week */			
		$result['statusWeek']=$this->get('service_apo_presenter')->getStatusWeek($idActionPlan, $result['startDate'], $result['endDate']);

        // render the template
        return $this->render('OptimusOptimusBundle:OccupancyActionPlan:occupancy_plan.html.twig', $result);
        //eturn $this->render('OptimusOptimusBundle:OccupancyActionPlan:test.html.twig', $result);
    }
	
	// Cambia el estado de un dia en el action plan 
	public function changeStatusDayAction(Request $request, $idOutputDay)
	{		
		$em = $this->getDoctrine()->getManager();		
		$outputDay=$em->getRepository('OptimusOptimusBundle:APOOutputDay')->findBy(array("id"=>$idOutputDay));
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
			$context="Action plan - Scheduling and management of the occupancy ";
			$visible=1;
			$ip=$this->container->get('request_stack')->getCurrentRequest()->getClientIp();
			
			$idActionPlan=(int)$elementsForm['idActionPlan'];
			
			$this->get('service_event')->createEvent($user, $textEvent, $context, $idActionPlan, 1, $ip, $request->request->get('idBuilding'), $newStatus);
		}
		
		return new Response("ok");
	}

    private function getRand(&$array) {
        $random = rand(0,1);
        $array['constraints'] = ($random < 0.1)?'Yes':'No';
        $array['on_off'] = ($random < 0.1)?'On':'Off';
        $array['no_people'] = ($random < 0.1)?rand(0, 100):0;
    }

    private function getDaysFromDate($from)
    {
        $aDays=array();
        for($i=0; $i<7; $i++)
        {
            $actDay=\DateTime::createFromFormat('Y-m-d', $from);
            $aDays[$i]=$actDay->modify(" +".$i." day")->format("D d-m");
        }
        return $aDays;
    }

    private function getPartitionConstraints(&$data, &$ordered, $idBuilding, $start_date, $Days, $constraints)
    {
        $start_date .= " 00:00:00";
        $current_date = new \DateTime();
        $current_date = $current_date->format('Y-m-d');
        $Dates=$this->getDateFormat($start_date);

        $allPartitions = array();
        $em = $this->getDoctrine()->getEntityManager();
        $partitionsBuilding = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building" => $idBuilding));
        if ($partitionsBuilding) {
            foreach ($partitionsBuilding as $partition) {
                $childs = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_BuildingPartitioning" => $partition->getId()));
                if ($childs)
                    if ($partition->getFkBuildingPartitioning() != NULL) {
                        $father = $em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findOneBy(array("id" => $partition->getFkBuildingPartitioning()));
                        if ($father->getFkBuildingPartitioning() == NULL) {
                            array_push($allPartitions, $partition->getPartitionName());
                            for($i=0; $i < count($Days); $i++) {
                                if (($constraints == '') or ($Dates[$i]->format('Y-m-d') < $current_date)) {
                                    $constraint = $em->getRepository('OptimusOptimusBundle:OccupancyConstraints')->findOutputByDay($partition->getPartitionName(), $Dates[$i]->format('Y-m-d H:i:s'));
                                    if ($constraint != null) {
                                        $data[$partition->getPartitionName()][$Days[$i]]['constraints'] = $constraint[0]->getMyConstraint();
                                    }
                                    else {
                                        $data[$partition->getPartitionName()][$Days[$i]]['constraints'] = 'No';
                                    }
                                    //$random = rand(0,1);
                                    //$data[$partition->getPartitionName()][$Days[$i]]['constraints'] = ($random < 0.1)?'Yes':'No';
                                }
                                else {
                                    $data[$partition->getPartitionName()][$Days[$i]]['constraints'] = array_shift($constraints);
                                    $constraint = $em->getRepository('OptimusOptimusBundle:OccupancyConstraints')->findOutputByDay($partition->getPartitionName(), $Dates[$i]->format('Y-m-d H:i:s'));
                                    if ($constraint != null) {
                                        $constraint[0]->setMyConstraint($data[$partition->getPartitionName()][$Days[$i]]['constraints']);
                                        $em->flush();
                                    }
                                    else {
                                        $output = new OccupancyConstraints();
                                        $output->setDate($Dates[$i]);
                                        $output->setSection($partition->getPartitionName());
                                        $output->setMyConstraint($data[$partition->getPartitionName()][$Days[$i]]['constraints']);
                                        //$em = $this->getDoctrine()->getManager();
                                        $em->persist($output);
                                        $em->flush();
                                    }
                                }
                                $data[$partition->getPartitionName()][$Days[$i]]['Early morning 1']['no_people'] = 0;
                                $data[$partition->getPartitionName()][$Days[$i]]['Early morning 1']['on_off'] = 'Off';
                                $data[$partition->getPartitionName()][$Days[$i]]['Early morning 2']['no_people'] = 0;
                                $data[$partition->getPartitionName()][$Days[$i]]['Early morning 2']['on_off'] = 'Off';
                                $data[$partition->getPartitionName()][$Days[$i]]['Early morning 3']['no_people'] = 0;
                                $data[$partition->getPartitionName()][$Days[$i]]['Early morning 3']['on_off'] = 'Off';
                                $data[$partition->getPartitionName()][$Days[$i]]['Morning-afternoon']['no_people'] = 0;
                                $data[$partition->getPartitionName()][$Days[$i]]['Morning-afternoon']['on_off'] = 'Off';
                                $data[$partition->getPartitionName()][$Days[$i]]['Late afternoon 1']['no_people'] = 0;
                                $data[$partition->getPartitionName()][$Days[$i]]['Late afternoon 1']['on_off'] = 'Off';
                                $data[$partition->getPartitionName()][$Days[$i]]['Late afternoon 2']['no_people'] = 0;
                                $data[$partition->getPartitionName()][$Days[$i]]['Late afternoon 2']['on_off'] = 'Off';
                            }
                            $ordered[$partition->getPartitionName()] = $partition->getEnergyConsumption();
                            $data[$partition->getPartitionName()]['max_capacity'] = $partition->getMaxCapacity();
                        }
                    }
            }
        }
        return $allPartitions;
    }

    private function partitionArray($sections) {
        $result = array();
        foreach($sections as $section) {
            $result[$section] = 1;
        }
        return $result;
    }

    private function getRandOccupancy($Days) {
        $occupancy = array();
        /* Open the input file: 'Estimated_occupancy.csv' */
        $configDirectories = array(__DIR__.'/extra_files');

        $locator = new FileLocator($configDirectories);
        $text_files = $locator->locate('Estimated_occupancy.csv', null, false);

        $file = file_get_contents($text_files[0]);

        $lines = explode("\n", $file);
        for($i=0; $i<count($Days);$i++) {
            $lines[$i] = explode(";", $lines[$i]);
        }
        for($i=0; $i<count($Days);$i++) {
            $day = \DateTime::createFromFormat('D d-m', $Days[$i])->format("N");
            $occupancy[$Days[$i]]['Early morning 1'] = $lines[0][$day-1];
            $occupancy[$Days[$i]]['Early morning 2'] = $lines[1][$day-1];
            $occupancy[$Days[$i]]['Early morning 3'] = $lines[2][$day-1];
            $occupancy[$Days[$i]]['Morning-afternoon'] = $lines[3][$day-1];
            $occupancy[$Days[$i]]['Late afternoon 1'] = $lines[4][$day-1];
            $occupancy[$Days[$i]]['Late afternoon 2'] = $lines[5][$day-1];
        }
        return $occupancy;
    }

    private function getSuggestions(&$data, $ordered, $occupancy, $Days, $sections,$idBuilding) {
        asort($ordered);
        //print_r($ordered);
        for($i=0; $i<count($Days);$i++) {
            $ordered2 = array();
            $ordered3 = array();
            foreach($ordered as $key=>$value) {
                //print $key;
                if($data[$key][$Days[$i]]['constraints'] == 'Yes') {
                    array_push($ordered3, $key);
                }
                else {
                    array_push($ordered2, $key);
                }
            }
            $final_sections[$Days[$i]] = array_merge($ordered3, $ordered2);
            //print_r($final_sections[$Days[$i]]);
            //print '<br>';
            for($j=0; $j<count($final_sections[$Days[$i]]); $j++) {
                //print $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                //print '<br>';
                $em = $this->getDoctrine()->getManager();
                $actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>2));
                $calculation=$em->getRepository('OptimusOptimusBundle:APCalculation')->findBy(array('fk_actionplan'=>$actionPlan[0]), array('dateCreation'=>'DESC'));
                $idCalculation=0;
				if(!empty($calculation)) 
				{
					$idCalculation=$calculation[0]->getId();
				}
				
                $output = $em->getRepository('OptimusOptimusBundle:APAdaptiveOutput')->findOutputByDate($Days[$i], $idCalculation);

                if (!empty($output)) {
                    $set_point = $output[0]->getSetPoint();
                } else {
                    $set_point = 21.5;
                }

                if ($data[$final_sections[$Days[$i]][$j]]['max_capacity'] < $occupancy[$Days[$i]]['Early morning 1']) {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 1']['no_people'] = $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 1']['on_off'] = '21.5';
                    $occupancy[$Days[$i]]['Early morning 1'] -= $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                } else {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 1']['no_people'] = $occupancy[$Days[$i]]['Early morning 1'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 1']['on_off'] = '21.5';
                    break;
                }
            }
            for($j=0; $j<count($final_sections[$Days[$i]]); $j++) {
                //print $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                //print '<br>';
                if ($data[$final_sections[$Days[$i]][$j]]['max_capacity'] < $occupancy[$Days[$i]]['Early morning 2']) {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 2']['no_people'] = $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 2']['on_off'] = $set_point;
                    $occupancy[$Days[$i]]['Early morning 2'] -= $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                } else {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 2']['no_people'] = $occupancy[$Days[$i]]['Early morning 2'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 2']['on_off'] = $set_point;
                    break;
                }
            }
            for($j=0; $j<count($final_sections[$Days[$i]]); $j++) {
                //print $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                //print '<br>';
                if ($data[$final_sections[$Days[$i]][$j]]['max_capacity'] < $occupancy[$Days[$i]]['Early morning 3']) {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 3']['no_people'] = $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 3']['on_off'] = $set_point;
                    $occupancy[$Days[$i]]['Early morning 3'] -= $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                } else {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 3']['no_people'] = $occupancy[$Days[$i]]['Early morning 3'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Early morning 3']['on_off'] = $set_point;
                    break;
                }
            }
            for($j=0; $j<count($final_sections[$Days[$i]]); $j++) {
                if ($data[$final_sections[$Days[$i]][$j]]['max_capacity'] < $occupancy[$Days[$i]]['Morning-afternoon']) {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Morning-afternoon']['no_people'] = $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Morning-afternoon']['on_off'] = $set_point;
                    $occupancy[$Days[$i]]['Morning-afternoon'] -= $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                } else {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Morning-afternoon']['no_people'] = $occupancy[$Days[$i]]['Morning-afternoon'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Morning-afternoon']['on_off'] = $set_point;
                    break;
                }
            }
            for($j=0; $j<count($final_sections[$Days[$i]]); $j++) {
                if ($data[$final_sections[$Days[$i]][$j]]['max_capacity'] < $occupancy[$Days[$i]]['Late afternoon 1']) {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 1']['no_people'] = $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 1']['on_off'] = $set_point;
                    $occupancy[$Days[$i]]['Late afternoon 1'] -= $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                }
                else {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 1']['no_people'] = $occupancy[$Days[$i]]['Late afternoon 1'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 1']['on_off'] = $set_point;
                    break;
                }
            }
            for($j=0; $j<count($final_sections[$Days[$i]]); $j++) {
                if ($data[$final_sections[$Days[$i]][$j]]['max_capacity'] < $occupancy[$Days[$i]]['Late afternoon 2']) {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 2']['no_people'] = $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 2']['on_off'] = $set_point;
                    $occupancy[$Days[$i]]['Late afternoon 2'] -= $data[$final_sections[$Days[$i]][$j]]['max_capacity'];
                }
                else {
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 2']['no_people'] = $occupancy[$Days[$i]]['Late afternoon 1'];
                    $data[$final_sections[$Days[$i]][$j]][$Days[$i]]['Late afternoon 2']['on_off'] = $set_point;
                    break;
                }
            }
        }
    }

    private function getDateFormat($date) {
        $Days = array();
        for($i=0; $i<14; $i++)
        {
            $actDay= new \DateTime($date);
            $Days[$i]=$actDay->modify(" +".$i." day");
        }
        return $Days;
    }
}
?>