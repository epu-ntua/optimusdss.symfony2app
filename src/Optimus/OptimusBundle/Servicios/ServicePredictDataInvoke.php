<?php

namespace Optimus\OptimusBundle\Servicios;

class ServicePredictDataInvoke {
  	
	const horizon=169;
	const window=169;
	
	public function PredictData($url='', $date='', $window='', $horizon='', $listofsensors)
	{
		// Common function for all prediction services:
		/*
		dump("PredictData [ Param1:".$date.
						 ", Param2:".$window.
						 ", Param3:".$horizon.
						 " ]");
		*/
		if($date == ''){ 
			$date = '2015-03-03T00:00:00Z'; 
			//dump("invoke prediction service error: parameter 'date' is not specified"); 
		}		
		if($window == ''){ 
			$window = window; 
			//dump("invoke prediction service error: parameter 'window' is not specified"); 
		}
		if($horizon == ''){ 
			$horizon = horizon; 
			//dump("invoke prediction service error: parameter 'horizon' is not specified"); 
		}
		if($url == '' || $listofsensors == ''){ 
			//dump("invoke prediction service error: parameter 'url' is not specified");
			return;
		}
		
		// Generate the full URL:
		$serviceCall = $url."?date=".$date."&window=".$window."&sensors=".$listofsensors;
		
		echo "Service call: ".$serviceCall."\n\n";
		
		// Invoke and extract data from the web service:
		return $this->InvokePredictionService($serviceCall);
	}	
	
	public function PredictDataWith2Windows($url='', $server = '', $dateForecast='', $dateHistorical='', $windowForecast='', $windowHistorical='', $horizon='', $listofsensors)
	{

		if($dateForecast == ''){ 
			$dateForecast = '2015-03-03T00:00:00Z'; 
			//dump("invoke prediction service error: parameter 'date' is not specified"); 
		}
		if($dateHistorical == ''){ 
			$dateForecast = '2015-03-03T00:00:00Z'; 
			//dump("invoke prediction service error: parameter 'date' is not specified"); 
		}
		if($windowForecast == ''){ 
			$windowForecast = 169; 
			//dump("invoke prediction service error: parameter 'window' is not specified"); 
		}
		if($windowHistorical == ''){ 
			$windowHistorical = 673; 
			//dump("invoke prediction service error: parameter 'window' is not specified"); 
		}
		if($horizon == ''){ 
			$horizon = horizon; 
			//dump("invoke prediction service error: parameter 'horizon' is not specified"); 
		}
		if($url == '' || $listofsensors == '' || $server == ''){ 
			//dump("invoke prediction service error: parameter 'url' is not specified");
			return;
		}
		
		// Generate the full URL:
		$serviceCall = $url."?sensors=".$listofsensors."&dateHistorical=".$dateHistorical."&dateForecast=".$dateForecast."&windowForecast=".$windowForecast."&server=".$server."&windowHistorical=".$windowHistorical;
		
		//echo "!!!!!!!Service call: ".$serviceCall."\n\n";
		
		// Invoke and extract data from the web service:
		return $this->InvokePredictionService($serviceCall);
	}
	
	public function PredictDataSwitchOnOff($url='', $date='', $window='', $listofsensors, $setpoint, $context)
	{
		// Common function for all prediction services:
		/*
		dump("PredictData [ Param1:".$date.
						 ", Param2:".$window.
						 ", Param3:".$horizon.
						 " ]");
		*/
		if($date == ''){ 
			$date = '2015-03-03T00:00:00Z'; 
			//dump("invoke prediction service error: parameter 'date' is not specified"); 
		}		
		if($window == ''){ 
			$window = window; 
			//dump("invoke prediction service error: parameter 'window' is not specified"); 
		}
		if($url == '' || $listofsensors == ''){ 
			//dump("invoke prediction service error: parameter 'url' is not specified");
			return;
		}
		
		// Generate the full URL:
		$serviceCall = $url."?date=".$date."&window=".$window."&sensors=".$listofsensors."&setpoint=".$setpoint."&context=".$context;
		
		echo "Service call: ".$serviceCall."\n\n";
		
		// Invoke and extract data from the web service:
		return $this->InvokePredictionService($serviceCall);
	}
	
	function InvokePredictionService($serviceCall)
	{
		$body = "";

		//dump($serviceCall);
		
		if($serviceCall != '') 
		{			
			$username = "admin";
			$password = "changeit";
			
			$additionalHeaders = "";
			$payloadName = "";
			
			//$response= file_get_contents($serviceCall);
			set_time_limit(0);
			$process = curl_init($serviceCall);
			
			curl_setopt($process, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
			curl_setopt($process, CURLOPT_HEADER, 1);
			curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
			curl_setopt($process, CURLOPT_TIMEOUT, 300);
			curl_setopt($process, CURLOPT_POST, 1);
			curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($process);
			
			$header_size = curl_getinfo($process, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			
			// final text to parse:
			$body = substr($response, $header_size);			
			
			//dump($body);
			//dump(curl_getinfo($process));
			curl_close($process);
		}
		
		return ($body);
	}	
}

?>