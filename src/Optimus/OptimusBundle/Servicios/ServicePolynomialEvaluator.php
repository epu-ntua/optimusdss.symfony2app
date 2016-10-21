<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Servicios\ServiceDataCapturing;

class ServicePolynomialEvaluator {
	
	protected $dataCapturing;
    protected $em;
	
	public function __construct(ServiceDataCapturing $dataCapturing, EntityManager $em)
    {
		$this->dataCapturing=$dataCapturing;
		$this->em=$em;
    }
	
	private  $pol_var = array(
		"mo" => array(0.000000,-3.184277,-3.933428,-4.714938,-3.622182,0.235010,7.240057,11.440317,14.966238,12.774963,10.230207,9.180058,5.723473,5.768472,8.060810,10.061600,11.200093,14.373062,19.145747,19.792658,14.678967,11.907780,8.634760,6.021683),
		"w" => array(0.000000,-2.469783,-3.733966,-4.754962,-4.218230,-1.032692, 4.683656, 8.200660,11.584248,10.103547, 7.206720, 5.614783, 2.148211, 1.499488, 3.406319, 5.242515, 7.083695, 9.999285, 13.751950, 14.359406, 10.737213, 7.943349, 5.119200, 1.910918),
		"sa" => array(0.00000000,-3.21964333,-4.34773333,-4.99551000,-5.38432333,-4.53318333,-3.15825667,-1.67394000, 0.80594000,-0.04342667,-0.20860000,-2.88057333,-6.52690000,-8.86716000,-8.45001333,-4.98857000,-1.78272333,1.44386333,6.44233667,8.37543000,3.01276667,-0.05430000,-2.80445000,-5.66459000),		
		"su" => array(0.0000000,-3.9514600,-6.3374833,-8.4799100,-9.9643900,-8.4622133,-6.9843233,-7.3073433,-3.5862400,0.2628400,3.7989333,3.9092100,0.0211900,-1.6831767,-1.7611867,0.8491167,3.4691167,5.8207300,9.6490867,11.6757933,8.9562600,6.9483267,3.0549400,-2.3351033));		
 
	private $f_value = array(
		"mo" => array (3.4309594, 0.7767282),
		"w" => array (3.4309594, 0.7767282),
		"sa" => array (8.9641,0.7829),
		"su" => array (5.3666,0.8149));		
	
					
	private function polyFit($X, $Y, $n) {
		$day_data = array();
	
		for ($i = 0; $i < sizeof($X); $i++) {
			for ($j = 0; $j <= $n; $j++){
				$A[$i][$j] = pow($X[$i], $j);
			}
	}
	
	
	
	for ($i = 0; $i < sizeof($A); $i++){
		$result = 0;
		for ($j = 0; $j <= $n; $j++){
			$result+= $A[$i][$j] * $Y[$j];
		}
		$day_data[$i] = $result;
	}
	
	return $day_data;
 }
		
	
	
	private function firstValue ($data,$dayType){
		
		$first_value = $this->f_value[$dayType];
		for ($i = 0; $i < sizeof($first_value); $i++) {
			$C[$i] = pow($data, $i);
		}
		
		$result = 0;
		
		for ($i = 0; $i < sizeof($first_value); $i++) {
			$result+= $C[$i]*$first_value[$i];
		}
		return $result;
	}
		

	private function evalDayPrices ($data,$dayType){
		$first = $this->firstValue ($data,$dayType);
		$polinomial_var = $this->pol_var[$dayType];
		
		for ($i = 0; $i < sizeof($polinomial_var); $i++) {
			$day_data[$i] = $first  + $polinomial_var[$i];
			//echo $day_data[$i];
		}
		return $day_data;
	}
	
	public function evalWeekPrices ($nameSensor, $idBuilding){
	
		$timestamp = time();

		$from = date('D', $timestamp);
						
		$dataHistoric=$this->dataCapturing->getDataFromDate(date("Y-m-d H:i:s"), date("Y-m-d")." 00:00:00", date("Y-m-d")." 23:00:00", $nameSensor, 'variable' , $idBuilding );

		$ff = array();
		
		if(count($dataHistoric) > 0)
		{
			//we get the last value of todays' monitored data
			$n = count($dataHistoric[0]['values']);
			$data = $dataHistoric[0]['values'][$n-1]['value'];
			
			
			//dump($dataHistoric);
			echo " DAta:   ".$data;
			
			for ($day = 1; $day < 8; $day++){
				if ($day >= 1 && $day <=5)
					$dayType = ($day == 1)?"mo":"w";
				else 
					$dayType = ($day == 6)?"sa":"su";
				$ff[$day] = $this->evalDayPrices ($data,$dayType);
				$data <- $ff[$day][23];
			}	
		}
		
		return ($ff);
		
	}
  
}//End of class
?>