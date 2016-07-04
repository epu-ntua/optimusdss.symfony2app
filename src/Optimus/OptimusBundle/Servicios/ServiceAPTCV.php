<?php
/**
 * Created by PhpStorm.
 * User: CHRISTINE
 * Date: 25-Nov-15
 * Time: 6:11 PM
 */

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APTCVOutput;
use Optimus\OptimusBundle\Entity\FeedbackOutput;
use Optimus\OptimusBundle\Servicios\Util\TCV\PMVCalculator;
use Optimus\OptimusBundle\Servicios\Util\TCV\LinearParameters;
use Optimus\OptimusBundle\Servicios\Util\TCV\ThermalSensation;
use Optimus\OptimusBundle\Servicios\ServiceOntologia;

class ServiceAPTCV {

    protected $em;
	protected $ontologia;
	
	
	private static $sensor_outdoorTemperature_name = "Outdoor Temperature";
	private static $sensor_humidity_name = "Humidity";
	private static $sensor_userFeedback_name = "User Feedback";

	public function getOutdoorTemperatureName(){return self::$sensor_outdoorTemperature_name;}
	public function getHumidityName(){return self::$sensor_humidity_name;}
	public function getUserFeedbackName(){return self::$sensor_userFeedback_name;}
	
	
    //The remote endpoint where TCV responses are stored
    protected $endpoint = "http://optimusdss.epu.ntua.gr:8890/sparql";
    //Minimum number of responses that must be given per hour
    protected $feedback_cutoff = 1;
    //The step of the triangular function
    protected $triangular_step = 0.5;
    //accuracy of the proposed temperature
    protected $temperature_accuracy = 0.2;
    //constant indicators
    protected $relative_air_velocity = 0.15;
    protected $clothing = 0.5;
    protected $metabolic_rate = 1.1;

    public function __construct(EntityManager $em,
								ServiceOntologia $ontologia)
    {
        $this->em=$em;
		$this->ontologia=$ontologia;
    }

    /*
     * Execute the SPARQL query against the service endpoint
     * Returns the bindings produced by the query
     */
    private function execute_sparql_query($query)
    {
        $ch = curl_init($this->endpoint . '?query=' . urlencode($query));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/sparql-results+json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $body = trim(curl_exec($ch));
        $result = json_decode($body);
        return $result->{'results'}->{'bindings'};
    }

    /*
     * Return a list of date strings in the week that starts at $start_date
     * e.g -> ['20150615', '20150616', '20150617', '20150618', '20150619']
     */
    private function get_week_dates($start_date)
    {
        $result = array();
        for ($i=0; $i<7; $i++) {
            $date = getdate(strtotime(date("Ymd", strtotime($start_date)) . " +$i day"));
            $result[$i] = $date["year"] . sprintf("%02d", $date["mon"]) . sprintf("%02d", $date["mday"]);
        }
        return $result;
    }

    /*
     * Construct the filters to only get instances with the $city and a date in the $dates list in their URI
     * Returns the SPARQL "FILTER" section of the query
     */
    private function get_filters($city, $dates, $temperature)
    {
        //Filter only the week days
        $filter = "FILTER ((regex(str(?o), '$city', 'i')) && (";
        if ($temperature) {
            $filter .= "regex(str(?o), 'bms', 'i')) && (";
        }
        for ($i=0; $i<count($dates); $i++) {
            $filter .= "(regex(str(?o), '$dates[$i]', 'i'))";
            if ($i < count($dates) - 1) {
                $filter .= "||";
            }
        }
        $filter .= "))";

        return $filter;
    }


    private function time_filter($dates) {
        $first_date = $dates[0][0] . $dates[0][1] . $dates[0][2] . $dates[0][3] . '-' . $dates[0][4] . $dates[0][5] . '-' . $dates[0][6] . $dates[0][7];
        $last_date = end($dates)[0] . end($dates)[1] . end($dates)[2] . end($dates)[3] . '-' . end($dates)[4] . end($dates)[5] . '-' . end($dates)[6] . end($dates)[7];
        $filter = "FILTER ((xsd:date(?time)>=xsd:date('$first_date')) && (xsd:date(?time)<=xsd:date('$last_date')))";
        return $filter;
    }

    /*
     * Get the first value for each day and hour
     */
    private function datetime_values($array)
    {
        $response = array();
        foreach ($array as $record) {
            $y_start = strlen($record->{'o'}->{'value'}) - 14;
            $dt = substr($record->{'o'}->{'value'}, $y_start, 8);
            $hr = intval(substr($record->{'o'}->{'value'}, $y_start + 8, 2));
            if (!array_key_exists($dt, $response)) {
                $response[$dt] = array();
            }
            if (!array_key_exists($hr, $response[$dt])) {
                $response[$dt][$hr] = $record->{'v'}->{'value'};
            }
        }
        return $response;
    }

    private function get_cond($dates, $actual_temperature, $actual_humidity) {
        $conditions = array();
        foreach($dates as $date) {
            $conditions[$date] = array();

            /*Working hours*/
            for ($hour=7; $hour<=21; $hour++) {
                $conditions[$date][$hour] = array();

                if (!isset($actual_temperature[$date][$hour])) {
                    continue;
                }
                $conditions[$date][$hour]['air_temperature'] = $actual_temperature[$date][$hour];

                if (!isset($actual_humidity[$date][$hour])) {
                    continue;
                }
                $conditions[$date][$hour]['relative_humidity'] = $actual_humidity[$date][$hour];
            }
        }
        return $conditions;
    }


    /*
     * Return an array of all sensor data for the $dates specified
     */
    private function get_pmv($pmv_calc, $dates, $actual_temperature, $actual_humidity, $clothing, $metabolic_rate)
    {
        $pmv = array();
        foreach($dates as $date) {
            $pmv[$date] = array();

            /*Working hours*/
            for ($hour=7; $hour<=21; $hour++) {
                if (!isset($actual_temperature[$date][$hour])) {
                    //array_push($air_temperatures, '17.5');
                    $pmv[$date][$hour] = null;
                    continue;
                }
                $air_temperature = $actual_temperature[$date][$hour];

                $mean_radiant_temperature = $air_temperature;
                if (!isset($actual_humidity[$date][$hour])) {
                    //array_push($relative_humidities, '-25.0');
                    $pmv[$date][$hour] = null;
                    continue;
                }
                $relative_humidity = $actual_humidity[$date][$hour];

                $pmv[$date][$hour] = $pmv_calc->pmv($air_temperature, $mean_radiant_temperature, $this->relative_air_velocity, $relative_humidity, $clothing, $metabolic_rate, 0);
            }
        }

        return $pmv;
    }

    /*
     * Returns feedback from the users of the Validator website
     */
    private function get_feedback($tcv_records, $dates, $section)
    {	
        $this_records = array();
        foreach ($tcv_records as $record) {
            if (strpos($record->{'r_building_type'}->{'value'}, $section) !== FALSE) {
                array_push($this_records, $record);
            }
        }
        //initialize feedback array
        $feedback = array();
        //foreach date
        foreach ($dates as $date) {
            //initialize feedback array
            $feedback[$date] = array();
            $datetime = $date[0] . $date[1] . $date[2] . $date[3] . '-' . $date[4] . $date[5] . '-' . $date[6] . $date[7] . 'T';
            /*Working hours*/
            $sensation = new ThermalSensation();
            for ($hour = 0; $hour <= 23; $hour++) {
                if (!array_key_exists($hour, $feedback[$date])) {
                    $feedback[$date][$hour] = ['size' => 0, 'value' => 0];
                }
                $datetime_str = $datetime . $hour;
                foreach ($this_records as $record) {
                    if (strpos($record->{'time'}->{'value'}, $datetime_str) !== FALSE) {
                        $v = $sensation->from_string($record->{'v'}->{'value'})->get_sensation();
                        $step = 0;
                        if (($hour >= 7) && ($hour <= 21 )) {
                            $feedback[$date][$hour]['value'] += $v;
                            $feedback[$date][$hour]['size']++;
                        }
						
						$step++;
						if ($v > 0) {
							$v -= $this->triangular_step;
						} else {
							$v += $this->triangular_step;
						}
                        while ($v != 0) {
                            
                            $prev = $hour - $step;
                            if (($prev >= 7) && ($prev <= 21)) {
                                $feedback[$date][$prev]['value'] += $v;
                                $feedback[$date][$prev]['size']++;
                            }

                            $next = $hour + $step;
                            if (($next >= 7) && ($next <= 21)) {
                                if (!array_key_exists($next, $feedback[$date])) {
                                    $feedback[$date][$next] = ['size' => 0, 'value' => 0];
                                }
                                $feedback[$date][$next]['value'] += $v;
                                $feedback[$date][$next]['size']++;
                            }
							
							$step++;
                            if ($v > 0) {
                                $v -= $this->triangular_step;
                            } else {
                                $v += $this->triangular_step;
                            }
                        }
                    }
                }
            }

        }
        return $feedback;
    }

    private function create_date_format($date, $hour) {
        $hour_str = sprintf('%02s', $hour);
        $date = substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6) . " ".$hour_str.":00:00";
        $date= new \DateTime($date);
        return $date ;
    }

    private function saveData($actual_temperature, $actual_humidity, $average_humidity, $tcv_records_prev, $tcv_records_curr, $prev_dates, $curr_dates, $sections, $pmv_calc, $idBuilding, $calculation) {
		foreach ($sections as $key=>$value) {
            if (is_array($value)) {
                $this->saveData($actual_temperature, $actual_humidity, $average_humidity, $tcv_records_prev, $tcv_records_curr, $prev_dates, $curr_dates, $value, $pmv_calc, $idBuilding, $calculation);
            }
            else {
                $prev_feedback = $this->get_feedback($tcv_records_prev, $prev_dates, $value);
                $current_feedback = $this->get_feedback($tcv_records_curr, $curr_dates, $value);
                //create a PMV calculator
                $pmv_calc[$value] = new PMVCalculator();
                //get initial conditions in an array of dates and times
                $pmv = $this->get_pmv($pmv_calc[$value], $prev_dates, $actual_temperature, $actual_humidity, $this->clothing, $this->metabolic_rate);
                /* PHASE 3 - GET OMV/AMV PAIRS */
                $points = array();
                foreach ($prev_dates as $date) {
                    for ($hour = 7; $hour <= 21; $hour++) {
                        if (($prev_feedback[$date][$hour]['size'] >= $this->feedback_cutoff) && isset($pmv[$date][$hour])) {
                            array_push($points, [
                                'x' => $prev_feedback[$date][$hour]['value'] / $prev_feedback[$date][$hour]['size'],
                                'y' => $pmv[$date][$hour]
                            ]);
                        }
                    }
                }

                if (count($points) > 0) { //if there's any input
                    //calculate slope and intercept of these points
                    $lp = new LinearParameters($points);
						
                    /* PHASE 4 - WHAT-IF ANALYSIS */

                    $min_distance = null;
                    $proposed_temperature = 0;

                    // try all temperatures between 0 and 50 degrees
                    // and find where the distance from the actual PMV minimizes
                    for ($t = 0; $t <= 50; $t += $this->temperature_accuracy) {
                        $pmv_val = $pmv_calc[$value]->pmv($t, $t, $this->relative_air_velocity, $average_humidity, $this->clothing, $this->metabolic_rate, 0);
                        $pmv_diff = abs($pmv_val - $lp->get_intercept());

                        if (($min_distance === null) || ($pmv_diff < $min_distance)) {
                            $proposed_temperature = $t;
                            $min_distance = $pmv_diff;
                        }
                    }
                    //print "Linear Parameters: Ekane " . (microtime(true)-$start8) . '<br>';
                    $proposed_temperature = round($proposed_temperature * 100) / 100;
                    $proposed_temperature = number_format($proposed_temperature, 1, '.', '');
                } else {
                    $proposed_temperature = 0.0;
                }

                foreach ($curr_dates as $date) {
                    $date_obj = $this->create_date_format($date, 0);
                    //$actionPlan = $this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findOneBy(array("fk_Building" => $idBuilding, "type" => $calculation));
                    $idCalculation = $calculation->getId();
                    $output = $this->em->getRepository('OptimusOptimusBundle:APTCVOutput')->findOutputByDate($date_obj->format('Y-m-d H:i:s'), $idCalculation, $value);

                    if ((!$output) && ($proposed_temperature != 0.0)) {
                        $output = new APTCVOutput();
                        $output->setDate($date_obj);
                        $output->setSection($value);
                        $output->setFkApCalculation($calculation);
                        $output->setProposedTemperature($proposed_temperature);
                        $this->em->persist($output);
                        $this->em->flush();
                    }
                    //$output = $this->em->getRepository('OptimusOptimusBundle:APTCVOutput')->findOutputByDate($date_obj->format('Y-m-d H:i:s'), $idCalculation, $value);
                    //$outputId = $output[0]->getId();
                    for ($hour = 0; $hour < 24; $hour++) {
                        $dates[$hour] = $this->create_date_format($date, $hour);
                        $feedback = $this->em->getRepository('OptimusOptimusBundle:FeedbackOutput')->findOutputByFullDate($dates[$hour]->format('Y-m-d H:i:s'), $value);
                        if ((!$feedback) && ($date == $curr_dates[0]) && ($current_feedback[$date][$hour]['size'] != 0) ) {
                            $feedback = new FeedbackOutput();
                            $feedback->setFullDate($dates[$hour]);
							$feedback->setSection($value);
							$feedback->setFkApCalculation($calculation);
                            $feedback->setFeedback($current_feedback[$date][$hour]['value']);
                            $feedback->setFeedbackSize($current_feedback[$date][$hour]['size']);
                            $this->em->persist($feedback);
                            $this->em->flush();
                        }
                    }
                }
				foreach ($prev_dates as $date) {
                    for ($hour = 0; $hour < 24; $hour++) {
                        $dates[$hour] = $this->create_date_format($date, $hour);
                        $feedback = $this->em->getRepository('OptimusOptimusBundle:FeedbackOutput')->findOutputByFullDate($dates[$hour]->format('Y-m-d H:i:s'), $value);
                        if ((!$feedback) && ($prev_feedback[$date][$hour]['size'] != 0) ) {
                            $feedback = new FeedbackOutput();
                            $feedback->setFullDate($dates[$hour]);
							$feedback->setSection($value);
							$feedback->setFkApCalculation($calculation);
                            $feedback->setFeedback($prev_feedback[$date][$hour]['value']);
                            $feedback->setFeedbackSize($prev_feedback[$date][$hour]['size']);
                            $this->em->persist($feedback);
                            $this->em->flush();
                        }
                    }
                }
            }
        }
    }

    /*
     * Run the service
     * The result is an array containing
     *   - Input from sensors (e.g 'actual_humidity', 'air_temperature')
     *   - Feedback from TCV ('feedback')
     *   - The proposed temperature ('proposed_temperature')
     */
    public function invoke_service($idBuilding, $start_date, $idAPType, $calculation, $sections) {
        /* Get initial data about the building */
        $building = $this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
        $city_key = str_replace(" ", "_", strtolower((trim($building->getCity()))));
        $name_key = $city_key . '_' . str_replace(" ", "_", strtolower((trim($building->getName()))));
		if($name_key === "savona_colombo-pertini_school")
			$name_key = "savona_school";
		
        /* PHASE 1 -- PREDICTION */
        //get dates in previous week
        $prev_start = \DateTime::createFromFormat('Y-m-d H:i:s', $start_date)->modify("-7 day")->format("Y-m-d");
        $prev_dates = $this->get_week_dates($prev_start);
		
		/*
        $filter = $this->get_filters($city_key, $prev_dates, True);

        //get air temperature
        $query = "SELECT ?o ?v WHERE { ?o a <http://optimus-smartcity.eu/ontology-releases/eu/semanco/ontology/optimus.owl#Air_TemperatureSensorOutput>. $filter ?o <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?v.}";
        //$query = "SELECT ?o ?v WHERE { ?o a <http://optimus-smartcity.eu/ontology-releases/eu/semanco/ontology/optimus.owl#Air_TemperatureSensorOutput>. FILTER ((regex(str(?o), 'sant_cugat/sensoroutput/bms*20151203|20151204|20151205|20151206|20151207|20151208|20151209', 'i')) ). ?o <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?v }";
        $actual_temperature = $this->datetime_values($this->execute_sparql_query($query));		
		
        $filter = $this->get_filters($city_key, $prev_dates, False);

        //get relative humidity values
        $query = "SELECT ?o ?v WHERE { ?o a <http://optimus-smartcity.eu/ontology-releases/eu/semanco/ontology/optimus.owl#HumiditySensorOutput>. $filter ?o <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?v.}";
        $actual_humidity = $this->datetime_values($this->execute_sparql_query($query));
		*/
		
		
		$actionPlan=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>$idAPType));
		$idActionPlan=$actionPlan[0]->getId();
		$sensor_temperature = $this->em->getRepository('OptimusOptimusBundle:APSensors')->findOneBy(array("fk_actionplan"=>$idActionPlan, "name"=>self::$sensor_outdoorTemperature_name));
		$idSensor = $sensor_temperature->getFkSensor()->getId();

		$start = \DateTime::createFromFormat('Y-m-d H:i:s', $start_date)->modify("-7 day");
		$end=\DateTime::createFromFormat('Y-m-d H:i:s', $start_date)->modify("+0 day");

		$array_ret = $this->ontologia->getDataFromSensorList($start, $end, 168, $idSensor);
		
		
		for($i=0; $i<7; $i++){
			$allzeroTemp = true;
			for($j=0; $j<24; $j++){
				$actual_temperature[$prev_dates[$i]][$j] = (string)$array_ret[$i*24 + $j][0];
				if($actual_temperature[$prev_dates[$i]][$j] != "0"){
					$allzeroTemp = false;
				}
			}
			if($allzeroTemp == true){
				for($j=0; $j<24; $j++){
					$actual_temperature[$prev_dates[$i]][$j] = null;
				}
			}
		}
		
		$sensor_humidity = $this->em->getRepository('OptimusOptimusBundle:APSensors')->findOneBy(array("fk_actionplan"=>$idActionPlan, "name"=>self::$sensor_humidity_name));
		$idSensor = $sensor_humidity->getFkSensor()->getId();

		$array_ret = $this->ontologia->getDataFromSensorList($start, $end, 168, $idSensor);
		
		
		for($i=0; $i<7; $i++){
			$allzeroHum= true;
			for($j=0; $j<24; $j++){
				$actual_humidity[$prev_dates[$i]][$j] = (string)$array_ret[$i*24 + $j][0];
				if($actual_humidity[$prev_dates[$i]][$j] != "0"){
					$allzeroHum = false;
				}
			}
			if($allzeroHum == true){
				for($j=0; $j<24; $j++){
					$actual_humidity[$prev_dates[$i]][$j] = null;
				}
			}
		}
		

        //calculate average humidity
        $actual_humidity_sum = 0;
        $cnt = 0;
        foreach ($actual_humidity as $date_humidity) {
            foreach ($date_humidity as $humidity) {
                $cnt ++;
                $actual_humidity_sum += $humidity;
            }
        }
        if ($cnt) {
            $average_humidity = $actual_humidity_sum /$cnt;
        } else {
            $average_humidity = null;
        }

        //get dates in current week
        $curr_dates = $this->get_week_dates($start_date);

        $pmv_calc = array();

        /* PHASE 2 - TCV FEEDBACK */
        $feedback_filter = $this->time_filter($prev_dates);
        $query = "PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>\nPREFIX optimus: <http://www.optimus-smartcity.eu/ontology/>\nPREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>\nSELECT ?r ?time ?r_building_type ?v\nWHERE {?r a optimus:tcv_record.\n FILTER ((regex(str(?r), '$name_key', 'i')))\n?r optimus:building_type ?r_building_type.\n?r optimus:thermal_sensation ?v.\n?r sem:hasTimeStamp ?time. $feedback_filter\n}";
        $tcv_records_prev = $this->execute_sparql_query($query);		
		
        $feedback_filter = $this->time_filter($curr_dates);
        $query = "PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>\nPREFIX optimus: <http://www.optimus-smartcity.eu/ontology/>\nPREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>\nSELECT ?r ?time ?r_building_type ?v\nWHERE {?r a optimus:tcv_record.\n FILTER ((regex(str(?r), '$name_key', 'i')))\n?r optimus:building_type ?r_building_type.\n?r optimus:thermal_sensation ?v.\n?r sem:hasTimeStamp ?time. $feedback_filter\n}";
        $tcv_records_curr = $this->execute_sparql_query($query);
		
		
		$this->saveData($actual_temperature, $actual_humidity, $average_humidity, $tcv_records_prev, $tcv_records_curr, $prev_dates, $curr_dates, $sections, $pmv_calc, $idBuilding, $calculation);
	}
}
?>