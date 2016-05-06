<?php


namespace Optimus\OptimusBundle\Servicios;

class GestorOntologia {
  
	const mEndpoint = "http://winarc.housing.salle.url.edu:8080/sparql";		
	const baseURI = "http://winarc.housing.salle.url.edu:8080/sparql";
	
	public function getDataParameterFromOntology($from='', $to='', $sensor='')
	{
		$config_endpoint = array('remote_store_endpoint' => self::mEndpoint); 
		
		set_time_limit(0);
		ini_set('memory_limit','64M');
		
		$arc = new \ARC_ARC ();
		$store = $arc->getRemoteStore($config_endpoint);		
		$rows=array();
		if($from!='')
		{
			//$startDate=$from;			
			$startDate=explode(" ", $from);			
			$startDate=$startDate[0];
			/*$startDate=str_replace(" ", "T", $from);
			$startDate=$startDate."Z";*/
			
			
		}else			$startDate='2014-11-23';
		
		if($to!='')
		{
			//$endDate=$to;
			$endDate=explode(" ", $to);
			$endDate=$endDate[0];
			/*$endDate=str_replace(" ", "T", $to);
			$endDate=$endDate."Z";*/
		}else 			$endDate='2014-11-29';
		
		$sensorActual = $sensor;
		
		$query = "	select ?value ?datetime
					from <http://optimus-test>
					where {

					?obs25 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <".$sensorActual.">;
					 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?value];
						 <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

					FILTER (?datetime >= '".$startDate."'^^xsd:dateTime && ?datetime <= '".$endDate."'^^xsd:dateTime)
					} order by ?datetime

				";
		set_time_limit(0);			
		$rows = $store->query($query, 'rows');
		
		return $rows;
	}
		
}

?>
