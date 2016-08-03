<?php


namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\Sensor;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class ServiceOntologia {
  
	protected $mEndpoint = "http://winarc.housing.salle.url.edu:8080/sparql";		
	protected $graph = "http://optimus-test";
	protected $em;
	
    public function __construct($optimus_endpoint, $optimus_graph, EntityManager $em)
	{
		$this->mEndpoint = $optimus_endpoint;
		$this->graph = $optimus_graph;
		$this->em = $em;
	}			

				
	public function getDataParameterFromOntology($from='', $to='', $sensor='', $aggregation='SUM')
	{
		$config_endpoint = array('remote_store_endpoint' => $this->mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC2();
		//$store = $arc->getRemoteStore($config_endpoint);	
        $store = $arc->getComponent('SPARQL11RemoteStore', $config_endpoint);        
		$rows=array();
		if($from!='')
		{
			//$startDate=$from;			
			$startDate=explode(" ", $from);			
			$startDate=$startDate[0];
			/*$startDate=str_replace(" ", "T", $from);
			$startDate=$startDate."Z";*/
			
			
		}else			
			$startDate='2014-11-23';
		
		if($to!='')
		{
			//$endDate=$to;
			$endDate=explode(" ", $to);
			$endDate=$endDate[0];
			/*$endDate=str_replace(" ", "T", $to);
			$endDate=$endDate."Z";*/
		}else 			$endDate='2014-11-29';
		
		$sensorActual = $sensor;
		
		$aggr = $aggregation;
		
		if($aggr == 'DEDUCTION')
			$aggr = 'SAMPLE';
		
		if($aggr == 'BOOLEAN')
			$aggr = 'AVG';
		
		if($aggr == '' || !isset($aggr))
			$aggr = "SUM";
		

        //2015-10-13: New query to obtain aggregated data by hour
   		$query = "	    select ".$aggr."(?v) AS ?value CONCAT(?y, '-', ?m, '-', ?d, ' ', ?h, ':00:00') as ?datetime
                        from <".$this->graph.">
                        where {

                        ?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensorActual.">;
                         <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?v];
                             <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?dt].

                        FILTER (?dt >= '".$startDate."'^^xsd:dateTime && ?dt <= '".$endDate."'^^xsd:dateTime)

                         
                        } GROUP BY (year(?dt) AS ?y) (month(?dt) AS ?m) (day(?dt) AS ?d) (hours(?dt) AS ?h)

                        order by ?y ?m ?d ?h

                    

				";       

        set_time_limit(0);			
        
		$rows = $store->query($query, 'rows');
	
		//////////////////
		// Cleaning the dates
		$from = str_replace("T", " ", $from);
		$from = str_replace("Z", "", $from);
		
		$to = str_replace("T", " ", $to);
		$to = str_replace("Z", "", $to);
				
		
		$dStartDate=\DateTime::createFromFormat('Y-m-d H:i:s', $from);		
		$dEndDate=\DateTime::createFromFormat('Y-m-d H:i:s', $to);
		
		if($dStartDate == false) {
			$dStartDate=\DateTime::createFromFormat('Y-m-dTH:i:sZ', $from);		
		}
		if($dStartDate == false)
			$dEndDate=\DateTime::createFromFormat('Y-m-dTH:i:sZ', $to);		
		
		$dateDiff = $dStartDate->diff($dEndDate);
		$window=$dateDiff->days;
		
		$window *= 24;
				
		////////////////////////
		// 2. Initialize the array to be returned at the end
		$array_ret = array();
		
		for($i = 0; $i < $window; $i++) {
			$array_ret[$i] = array();
			
			$array_ret[$i]["value"] = 0;

			//datetime
			$array_ret[$i]["datetime"] = \DateTime::createFromFormat('Y-m-d H:i:s', $from);
			$array_ret[$i]["datetime"]->add(date_interval_create_from_date_string($i.' hours'));
		}
		
		$lastInx = -100;
		$inx = -1;
		////////////////////////
		// 3.1 Placing the data in the proper place.
		for($k = 0; $k < count($rows); $k++) {
			
			if($k < count($array_ret)) {
								
				$inx = $this->search_in_array($array_ret, $rows[$k]["datetime"], $inx);
				
				if($inx > -1 ) {
					$array_ret[$inx]["value"] = $rows[$k]["value"];
				
					if($aggregation == 'BOOLEAN') {
						if($array_ret[$inx]["value"] < 1) $array_ret[$inx]["value"] = 0;
					}
					else {
						if(($inx - $lastInx) > 1) {
							//we have a gap
							if($lastInx < 0) {
								//The first valuesa are a gap
								
								for($g = $inx-1; $g >= 0; $g--) {
									$array_ret[$g]["value"] = $array_ret[$inx]["value"];
								}
							}
							else {
								if(is_numeric($array_ret[$inx]["value"])) {
									$step = ($array_ret[$inx]["value"] - $array_ret[$lastInx]["value"]) / ($inx - $lastInx);

																
									$n = 1;
									for($g = $lastInx+1; $g < $inx; $g++) {
										$array_ret[$g]["value"] = $array_ret[$lastInx]["value"] + ($step * $n);
										$n++;
									}
								}
							}
						}
					}			
					$lastInx = $inx;
				}
			}
		}
		
		//We fill the last gap if it exists
		if($inx > -1) {
			if(is_numeric($array_ret[$inx]["value"])) {
				if($inx < count($array_ret)-1) {
					for($g = $inx+1; $g < count($array_ret); $g++) {
						$array_ret[$g]["value"] = $array_ret[$inx]["value"];
					}
				}
			}
		}
		
		if($aggregation == 'DEDUCTION') {
			
			for($k = count($array_ret)-1; $k > 0; $k--) {
				if($array_ret[$k]["value"] > 0)
					$array_ret[$k]["value"] = $array_ret[$k]["value"] - $array_ret[$k-1]["value"];
				if($array_ret[$k-1]["value"] == 0)
					$array_ret[$k]["value"] = 0;
			}
			//The first value is always a 0
			$array_ret[0]["value"] = 0;
		}
	
		
		return $array_ret;
	} 	
	
	
	public function getDataParameterFromVirtuoso($from='', $to='', $sensor='', $aggregation='SUM')
	{
		$config_endpoint = array('remote_store_endpoint' => $this->mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC2 ();
		//$store = $arc->getRemoteStore($config_endpoint);	
        $store = $arc->getComponent('SPARQL11RemoteStore', $config_endpoint);        
		$rows=array();
		
		$from = str_replace(" ", "T", $from);
		$from = str_replace("Z", "", $from);
		$from .= "Z";
		
		$to = str_replace(" ", "T", $to);
		$to = str_replace("Z", "", $to);
		$to .= "Z";
		
		$aggr = $aggregation;
		
		if($aggr == 'BOOLEAN')
			$aggr = 'AVG';
		
		if($aggr == '' || !isset($aggr))
			$aggr = "SUM";
		
		
		$sensorActual = $sensor;
		/*
		$query = "	select ?value ?datetime
					from <".$this->graph.">
					where {

					?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensor.">;
					 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?value];
						 <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

					FILTER (?datetime >= '".$from."'^^xsd:dateTime && ?datetime <= '".$to."'^^xsd:dateTime)
					} order by ?datetime

				";
                */
        //2015-10-13: New query to obtain aggregated data by hour
   		$query = "	    select ".$aggr."(?v) AS ?value CONCAT(?y, '-', ?m, '-', ?d, ' ', ?h, ':00:00') as ?datetime
                        from <".$this->graph.">
                        where {

                        ?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensor.">;
                         <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?v];
                             <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?dt].

                        FILTER (?dt >= '".$from."'^^xsd:dateTime && ?dt <= '".$to."'^^xsd:dateTime)

                         
                        } GROUP BY (year(?dt) AS ?y) (month(?dt) AS ?m) (day(?dt) AS ?d) (hours(?dt) AS ?h)

                        order by ?y ?m ?d ?h

                    

				";       

		//var_dump($query);
        set_time_limit(0);			
        
		$rows = $store->query($query, 'rows');

		return $rows;
	} 	
	
	
	public function getAggregatedFromVirtuoso($from='', $to='', $sensor='', $aggregation='SUM' )
	{
		$config_endpoint = array('remote_store_endpoint' => $this->mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC2 ();
		//$store = $arc->getRemoteStore($config_endpoint);	
        $store = $arc->getComponent('SPARQL11RemoteStore', $config_endpoint);        
		$rows=array();
		
		$from = str_replace(" ", "T", $from);
		$from = str_replace("Z", "", $from);
		$from .= "Z";
		
		$to = str_replace(" ", "T", $to);
		$to = str_replace("Z", "", $to);
		$to .= "Z";
		
		$sensorActual = $sensor;

		$aggr = $aggregation;
		
		if($aggr == 'DEDUCTION')
			$aggr = 'SAMPLE';
		
		if($aggr == 'BOOLEAN')
			$aggr = 'AVG';
		
		if($aggr == '' || !isset($aggr))
			$aggr = "SUM";
		
        //2015-10-13: New query to obtain aggregated data by hour
   		$query = "	select ".$aggr."(?value) AS ?total count(?value) AS ?numvalues WHERE {
						select sum(?v) AS ?value CONCAT(?y, '-', ?m, '-', ?d, ' ', ?h, ':00:00') as ?datetime
                        from <".$this->graph.">
                        where {

                        ?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensor.">;
                         <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?v];
                             <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?dt].

                        FILTER (?dt >= '".$from."'^^xsd:dateTime && ?dt <= '".$to."'^^xsd:dateTime)

                         
                        } GROUP BY (year(?dt) AS ?y) (month(?dt) AS ?m) (day(?dt) AS ?d) (hours(?dt) AS ?h)
					}
				";       

	//	var_dump($query);
        set_time_limit(0);			
        
		$rows = $store->query($query, 'rows');

		return $rows;
	} 	
	
    
	public function insertData($insert_statement) {
		$config_endpoint = array('remote_store_endpoint' => $this->mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC2 ();
		$store = $arc->getRemoteStore($config_endpoint);		
		$rows=array();

		$rows = $store->query($insert_statement, 'rows');
		
        $errors = $store->getErrors();
        var_dump("Rows: ");
        var_dump($errors);
	}
	
    
	public function checkSensor($sensor='', $lastdata='')
	{
		$config_endpoint = array('remote_store_endpoint' => $this->mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC2 ();
		$store = $arc->getRemoteStore($config_endpoint);		
		$rows=array();

		// We check data from yesterday
		$lastdata = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));   

		$query = "	select ?value ?datetime
					from <".$this->graph.">
					where {

					?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensor.">;
					 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?value];
						 <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

					FILTER (?datetime >= '".$lastdata."'^^xsd:dateTime)
					} order by desc (?datetime) limit 1

				";
		set_time_limit(0);			
		$rows = $store->query($query, 'rows');
       
		return $rows;
	}
	
	
	public function lastDataSensor($sensor='', $lastdata='')
	{
		$config_endpoint = array('remote_store_endpoint' => $this->mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC2 ();
		$store = $arc->getRemoteStore($config_endpoint);		
		$rows=array();

		// We check data from yesterday
		$lastdata = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-30, date("Y")));   

		$query = "	select ?value ?datetime
					from <".$this->graph.">
					where {

					?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensor.">;
					 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?value];
						 <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

					FILTER (?datetime >= '".$lastdata."'^^xsd:dateTime)
					} order by desc (?datetime) limit 1

				";
		set_time_limit(0);			
		$rows = $store->query($query, 'rows');
       
		return $rows;
	}


	
	public function lastNRecordsListOfSensors($from='', $to='', $window, $sensors='')
	{
        ////////////////////////
		// 1. Processing the sensors paramter
		
		$sensors_arr = explode ("_", $sensors);
		$numsensors = count($sensors_arr);
		$sensors_aggregation = array();
		
		for($j = 0; $j < $numsensors; $j++) {
			$nameSensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findOneById($sensors_arr[$j]);

			if ($nameSensor)
			{
				$sensors_arr[$j] = $nameSensor->getUrl();
				$sensors_aggregation[$j] = $nameSensor->getAggregation();
			}
		}		
		
		
		//var_dump("Num of sensors: ".$numsensors);
		//
		////////////////////////
		////////////////////////
		// 2. Initialize the array to be returned at the end
		$array_ret = array();
		
		for($i = 0; $i < $window; $i++) {
			$array_ret[$i] = array();
			
			for($j = 0; $j < ($numsensors); $j++) {
				$array_ret[$i][$j] = -1;
			}
			
			//datetime
			$array_ret[$i][$j] = \DateTime::createFromFormat('Y-m-d H:i:s', $from->format('Y-m-d H:i:s'));
			$array_ret[$i][$j]->add(date_interval_create_from_date_string($i.' hours'));
		}
		
		//
		////////////////////////
		// 3. Asking for data
		for($j = 0; $j < $numsensors; $j++) {

			$ret = $this->getDataParameterFromVirtuoso($from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s'), $sensors_arr[$j], $sensors_aggregation[$j]);
			
		//	var_dump($sensors_arr[$j]);
		//	var_dump($ret);
			$lastInx = -111;
			$inx = -1;
			////////////////////////
			// 3.1 Placing the data in the proper place.
			for($k = 0; $k < count($ret); $k++) {
				
				if($k < count($array_ret)) {
									
					$inx = $this->search_in_array($array_ret, $ret[$k]["datetime"], $inx);
										
					if($inx > -1 ) {
						$array_ret[$inx][$j] = $ret[$k]["value"];
					
						if($sensors_aggregation[$j] == 'BOOLEAN') {
							if($array_ret[$inx][$j] < 1) $array_ret[$inx][$j] = 0;
						}
						else {
							if(($inx - $lastInx) > 1) {
								//we have a gap
								if($lastInx < 0) {
									//The first valuesa are a gap
									
									for($g = $inx-1; $g >= 0; $g--) {
										$array_ret[$g][$j] = $array_ret[$inx][$j];
									}
								}
								else {
									if(is_numeric($array_ret[$inx][$j])) {
										$step = ($array_ret[$inx][$j] - $array_ret[$lastInx][$j]) / ($inx - $lastInx);

																	
										$n = 1;
										for($g = $lastInx+1; $g < $inx; $g++) {
											$array_ret[$g][$j] = $array_ret[$lastInx][$j] + ($step * $n);
											$n++;
										}
									}
								}
							}
						}			
						$lastInx = $inx;
					}
				}
			}
		}
		
		return $array_ret;
	}
	///////////////////////////////////////////////////////////////////////////////////
	// This method return data from sensors in a particular period of time. 
	// It takes into account if there are gaps betwen the data. It is filled with 0.
	// Input parameters:
	// - from: DateTime var with the starting date of the period to be returned
	// - to: DateTime var with the end date of the period to be returner
	// - window: int with the number of hours of the period (1 week: 168 hours).
	// - sensors: This parameteres are the IDs of the sensors needed separated by &. the list of sensors is ordered   
	//			   and each "predictedparameter" should know its own list of sensors needed
	// The method returns a multidimensional array of $window x count($sensors) dimension.
	//
	///////////////////////////////////////////////////////////////////////////////////	
	
	public function getDataFromSensorList($from='', $to='', $window, $sensors='') 
	{
		////////////////////////
		// 1. Processing the sensors paramter
		
		$sensors_arr = explode ("_", $sensors);
		$numsensors = count($sensors_arr);
		$sensors_aggregation = array();
		
		for($j = 0; $j < $numsensors; $j++) {
			$nameSensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findOneById($sensors_arr[$j]);

			if ($nameSensor)
			{
				$sensors_arr[$j] = $nameSensor->getUrl();
				$sensors_aggregation[$j] = $nameSensor->getAggregation();
			}
		}		
		
		
		//var_dump("Num of sensors: ".$numsensors);
		//
		////////////////////////
		////////////////////////
		// 2. Initialize the array to be returned at the end
		$array_ret = array();
		
		for($i = 0; $i < $window; $i++) {
			$array_ret[$i] = array();
			
			for($j = 0; $j < ($numsensors); $j++) {
				$array_ret[$i][$j] = 0;
			}
			
			//datetime
			$array_ret[$i][$j] = \DateTime::createFromFormat('Y-m-d H:i:s', $from->format('Y-m-d H:i:s'));
			$array_ret[$i][$j]->add(date_interval_create_from_date_string($i.' hours'));
		}
		
		//
		////////////////////////
		// 3. Asking for data
		for($j = 0; $j < $numsensors; $j++) {

			$ret = $this->getDataParameterFromVirtuoso($from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s'), $sensors_arr[$j], $sensors_aggregation[$j]);
			
		//	var_dump($sensors_arr[$j]);
		//	var_dump($ret);
			$lastInx = -111;
			$inx = -1;
			////////////////////////
			// 3.1 Placing the data in the proper place.
			for($k = 0; $k < count($ret); $k++) {
				
				if($k < count($array_ret)) {
									
					$inx = $this->search_in_array($array_ret, $ret[$k]["datetime"], $inx);
										
					if($inx > -1 ) {
						$array_ret[$inx][$j] = $ret[$k]["value"];
					
						if($sensors_aggregation[$j] == 'BOOLEAN') {
							if($array_ret[$inx][$j] < 1) $array_ret[$inx][$j] = 0;
						}
						else {
							if(($inx - $lastInx) > 1) {
								//we have a gap
								if($lastInx < 0) {
									//The first valuesa are a gap
									
									for($g = $inx-1; $g >= 0; $g--) {
										$array_ret[$g][$j] = $array_ret[$inx][$j];
									}
								}
								else {
									if(is_numeric($array_ret[$inx][$j])) {
										$step = ($array_ret[$inx][$j] - $array_ret[$lastInx][$j]) / ($inx - $lastInx);

																	
										$n = 1;
										for($g = $lastInx+1; $g < $inx; $g++) {
											$array_ret[$g][$j] = $array_ret[$lastInx][$j] + ($step * $n);
											$n++;
										}
									}
								}
							}
						}			
						$lastInx = $inx;
					}
				}
			}
		}
		
		return $array_ret;
	}
	


	private function search_in_array($array_ret, $inputdate, $lastIndex) {
		$inx = $lastIndex;
		
		$enddate=\DateTime::createFromFormat('Y-m-d H:i:s', $inputdate);
		
		//echo "Input ".$enddate->format('Y-m-d H:i:s')."<br>";
		
		for($k = 0; $k < count($array_ret); $k++) {
			
			//echo "arrr ".$array_ret[$k][count($array_ret[$k])-1]->format('Y-m-d H:i:s')."<br>";
			//echo "Diff ".$k.": ".$enddate->diff($array_ret[$k][count($array_ret[$k])-1])->format('%h')."<br>";
			
			$arrkeys =array_keys ($array_ret[$k]);
			
			if($array_ret[$k][$arrkeys[count($arrkeys)-1]] == $enddate)
				return $k;
			if($array_ret[$k][$arrkeys[count($arrkeys)-1]] > $enddate)
				break;
			
		}
		
		return -1;
	}
}

?>
