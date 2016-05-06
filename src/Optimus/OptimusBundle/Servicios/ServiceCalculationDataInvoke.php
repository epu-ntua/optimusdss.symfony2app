<?php

namespace Optimus\OptimusBundle\Servicios;

class ServiceCalculationDataInvoke {
  	
	const horizon=169;
	const window=169;
	
	public function CalculateData($url='', $date='', $window='', $horizon='',  $total_pv_area='', $ta='')
	{
		// Could be common for all calculation services or not?????
		/*	
		dump("CalculateData [ Param1:".$date.
						   ", Param2:".$window.
						   ", Param3:".$horizon.
						   ", Param4:".$total_pv_area.
						   ", Param5:".$ta.
						   " ]");
		*/
		if($date == ''){ 
			$date = '2015-03-03T00:00:00Z'; 
			//dump("invoke service error: parameter 'date' is not specified"); 
		}
		if($window == ''){ 
			$window = window; 
			//dump("invoke service error: parameter 'window' is not specified"); 
		}
		if($horizon == ''){ 
			$horizon = horizon; 
			//dump("invoke service error: parameter 'horizon' is not specified"); 
		}
		if($total_pv_area == ''){ 
			$total_pv_area = '0'; 
			//dump("invoke service error: parameter 'total_pv_area' is not specified"); 
		}
		if($ta == ''){ 
			$ta = '0'; 
			//dump("invoke service error: parameter 'ta' is not specified"); 
		}
		if($url == ''){ 
			//dump("invoke predict service error: parameter 'url' is not specified");
			return;
		}

		// Generate the full URL:
		$serviceCall = $url."?date=".$date."&window=".$window."&total_pv_area=".$total_pv_area."&ta=".$ta;
		
		// Invoke and extract data from the web service:
		return $this->InvokeCalculationService($serviceCall);
	}
	
	function InvokeCalculationService($serviceCall)
	{
		$body = "";

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
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_POST, 1);
			curl_setopt($process, CURLOPT_POSTFIELDS, $payloadName);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$response = curl_exec($process);
			
			$header_size = curl_getinfo($process, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			
			// final text to parse:
			$body = substr($response, $header_size);			
			
			//dump($body);
					
			curl_close($process);
		}
		
		return ($body);
	}	
}

?>