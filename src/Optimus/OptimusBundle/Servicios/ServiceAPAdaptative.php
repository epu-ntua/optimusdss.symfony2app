<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\Sensor;
use Optimus\OptimusBundle\Entity\APAdaptiveOutput;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;

class ServiceAPAdaptative {
    protected $em;
	protected $ontologia;
    //constant value
    protected $c = array(1, 0.8, 0.6, 0.5, 0.4, 0.3, 0.2);
    protected $window = 168;
    protected $data = array();

    private static $sensor_outdoorTemperature_name = "Outdoor Temperature";

    public function getOutdoorTemperatureName(){return self::$sensor_outdoorTemperature_name;}

    public function __construct(EntityManager $em,
								ServiceOntologia $ontologia)
    {       
		$this->em=$em;
		$this->ontologia=$ontologia;
    }

    private function calculate_set_point_temp($i) {
        $rmt = $this->data[$i][1];
		/*	if ($rmt < 14.0) {
				$spt = 21.5;
			} elseif ($rmt < 22.5) {
				$spt = 0.5861 * $rmt + 12.531;
			} else {
				$spt = 26.0;
			}
		*/
		if ($rmt <= 10.0) {
			$spt = 22.5;
		} 
		else {
			$spt = 0.302 * $rmt + 19.39;
		}
        return number_format((float)$spt, 1, '.', '');
    }

    private function calculate_dmt($i, $input, $date, $calculation) {
		//dump($input[0]);
        $sum = 0.0;
        $count = $i *24 + 23;
        for ($index = 23; $index >= 0; $index--) {
            $sum += $input[$count-$index][0];
        }
        $dmt = $sum/24;
        $this->data[$i][0] = number_format((float)$dmt, 1, '.', '');
        return $this->save_daily_mean($i, $date, $calculation);
    }

    private function calculate_rmt($i) {
        $rmt = 0.0;
        for ($j = 7; $j >= 1; $j--) {
            $rmt += $this->data[$i - $j][0] * $this->c[$j-1];
        }
        $rmt /= 3.8;
        $this->data[$i][1] = number_format((float)$rmt, 1, '.', '');
    }

    private function save_daily_mean($i, $date, $calculation) {
        $output = new APAdaptiveOutput();
        $output->setDate($date);
        $output->setDailyMean($this->data[$i][0]);
        $output->setSetPoint(0.0);
        $output->setFkApCalculation($calculation);
        return $output;
    }

    private function getDateFormat($date) {
        $Days = array();
        $date .= ' 00:00:00';
        for($i=0; $i<14; $i++)
        {
            $actDay= new \DateTime($date);
            $Days[$i]=$actDay->modify(" +".$i." day");
        }
        return $Days;
    }

    public function invoke_service($idBuilding, $this_date, $idAPType, $calculation) {
        $actionPlan=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>$idAPType));
        $idActionPlan=$actionPlan[0]->getId();
        $sensor = $this->em->getRepository('OptimusOptimusBundle:APSensors')->findOneBy(array("fk_actionplan"=>$idActionPlan, "name"=>self::$sensor_outdoorTemperature_name));
        $idSensor = $sensor->getFkSensor()->getId();

        $start_date = \DateTime::createFromFormat('Y-m-d H:i:s', $this_date)->modify("-7 day");
        $end_date=\DateTime::createFromFormat('Y-m-d H:i:s', $this_date)->modify("+6 day");

        //$service = new ServiceOntologia($this->mEndpoint, $this->graph, $this->em);
		/*dump($start_date);
		dump($end_date);
		dump($this->window);
		dump($idSensor); */
        //$array_ret = $service->getDataFromSensorList($start_date, $end_date, 2*$this->window, $idSensor);
		$array_ret = $this->ontologia->getDataFromSensorList($start_date, $end_date, 2*$this->window, $idSensor);
		dump($array_ret);
		//dump($array_ret);
        /*
        for($i = 0; $i < count($array_ret); $i++) {
            $row = $array_ret[$i][0];
            for($j = 1; $j < count($array_ret[$i])-1; $j++) {
                $row .= " ".$array_ret[$i][$j];
            }

            echo $row." ".$array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s')."<br>";
        }
        */

        $dates = $this->getDateFormat($start_date->format('Y-m-d'));

        for ($i = 0; $i < 14; $i++) {
            $data[$i] = array();
            $input = $this->em->getRepository('OptimusOptimusBundle:APAdaptiveOutput')->findOutputByDate($dates[$i]->format('Y-m-d H:i:s'), $idActionPlan);

            if (empty($input)) {
                for ($hour = 0; $hour < 24; $hour++) {
                    $array_ret[$i * 24 + $hour][0] = number_format((float)$array_ret[$i * 24 + $hour][0], 2, '.', '');
                    if ($hour == 23) {
                        $output = $this->calculate_dmt($i, $array_ret, $dates[$i], $calculation);
                        if ($i >= 7) {
                            $this->calculate_rmt($i);
                            $output->setSetPoint($this->calculate_set_point_temp($i));
                        }
                        $this->em->persist($output);
                        $this->em->flush();
                    }
                }
            }
            else {
                $dm = $input[0]->getDailyMean();
                $this->data[$i][0] = $dm;
                if (($i >= 7) && ($input[0]->getSetPoint() == 0.0)) {
                    $this->calculate_rmt($i);
                    $input[0]->setSetPoint($this->calculate_set_point_temp($i));
                    $this->em->flush();
                }
            }
        }
    }
}
?>

