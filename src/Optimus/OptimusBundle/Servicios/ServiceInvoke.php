<?php

namespace Optimus\OptimusBundle\Servicios;

class ServiceInvoke {
  	
	const horizon=168;
	const window=144;
	
	public function predict_data($predictedparameter='irradiation', $date='', $horizon=168)
	{
		
		if($date == '')
			$date = '2015-03-03T00:00:00Z';
				
		$prediction = $this->invoke_service($predictedparameter, $date, $horizon);
		
		return $prediction;		
	}
	
	function invoke_service($predictedparameter='irradiation', $date='', $window=144, $horizon=168)
	{
		$service = "";
		if($predictedparameter == 'power') {
			$service = "http://optimusdss.epu.ntua.gr:8080/RAWS/process/Forecasting_Prediction_model_PV_R?date=".$date."&window=".$window;			
		}
		
		$body = "";

		if($service != '') {			
			
			$username = "admin";
			$password = "changeit";
			
			$additionalHeaders = "";
			$payloadName = "";
			
			//$response= file_get_contents($service);
			set_time_limit(0);
			$process = curl_init($service);
			curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
			curl_setopt($process, CURLOPT_HEADER, 1);
			curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_POST, 1);
			curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($process);
			
			$header_size = curl_getinfo($process, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$body = substr($response, $header_size);
					
			curl_close($process);
			
		}
		
		return ($body);
	}
}

?>