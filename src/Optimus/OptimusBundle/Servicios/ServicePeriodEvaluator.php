<?php

//////////////////////////////////////////////////
/*PERIOD		WINTER		SUMMER		DAYS
 ----------------------------------------
 P1			18-22		11-15		Mon-Fri
 P2			The rest	The rest	Mon-Fri
 P3			00-08		00-08		Mon-Fri
 P4			18-22		11-15		Sat-Sun-Holly
 P5			The rest	The rest	Sat-Sun-Holly
 P6 			00-08		00-08		Sat-Sun-Holly */
//////////////////////////////////////////////////
/*
 * 1.- Get day of the week
 * 2.- Get month of the year
 * 3.- Get hour of the day
 */

namespace Optimus\OptimusBundle\Servicios;

use Optimus\OptimusBundle\Servicios\ServicePricesContainer;
 
class ServicePeriodEvaluator {
  
	private   	$dayPeriodAverage;
	private   	$expandedPeriodAverage;
	protected 	$pricesContainer;
  /**
   *
   */
   
	public function __construct(ServicePricesContainer $pricesContainer)
    {
        $this->pricesContainer=$pricesContainer;
    }
	
	public function getPriceContainer()
	{
		return $this->pricesContainer;
	}
   
    public function GetNextWeekForecast (){
		$now = time();
		$day 	= date( "w", $now);//returns day of the week 0 Sunday -> 6 Saturday
		$ff = array();
		for ($idx = 1; $idx < 8; $idx++){
			if (++$day > 6) $day = 0; //If tomorrow is monday then 0
			$ff[$idx]= array();
			for ($hour = 0;$hour < 24; $hour++){
			if ( $day == 6 || $day == 0){
				$ff[$idx][$hour]= $this->expandedPeriodAverage["h"][$hour];
			}
			else {
			$ff[$idx][$hour]= $this->expandedPeriodAverage["w"][$hour];
			}
		}
    }
    //print_r($ff);
    return $ff;
  }
  
   
	public function ExpandAveragePrices ()
	{
		$this->expandedPeriodAverage = array ( "w" => array (0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
                                            "h" => array (0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,));
		foreach (array_keys($this->dayPeriodAverage) as $dayType) {
			//print_r($dayType);
			$periods = $this->pricesContainer->getTariffType()->getPeriodsByType($dayType);
			 //BTL updates the loop to include an additional index.
			for ($i = 0, $idx = 0; $i < count($periods)-1; ++$i,$idx++) {
				for ($k = $periods[$i]; $k < $periods[$i+1]; $k++) {
					$this->expandedPeriodAverage [$dayType][$k] = $this->dayPeriodAverage [$dayType][$idx];
				}
			}
			/*
			for ($i = 0; $i < count($periods)-1; ++$i) {
				for ($k = $periods[$i]; $k < $periods[$i+1]; $k++) {
				  $this->expandedPeriodAverage [$dayType][$k] = $this->dayPeriodAverage [$dayType][0];
				}
			}
			*/
		}
		return $this->expandedPeriodAverage;
	}
	
	/**
	*
	* @param unknown $dayData
	*/
	private function CalculateAveragePerDayType ($dayData)
	{
  
		//For each row in $dayData get the daytype of the day and create a new array
		//with the average labeled with "w" or "h"  $dayTypeAverage[][]= (p1,p2,p3,p4)
		$this->dayPeriodAverage = array ("w" => array (0,0,0,0),"h" => array (0,0,0,0));
		
		$now = new \DateTime(); //mktime();
		$month 	= $now->format("m"); //date( "m", $now);//returns month of the year 01 January -> 12 December
		$year 	= $now->format("Y"); //date( "Y", $now);//returns month of the year 01 January -> 12 December
		$idDay = 0;
		
		
		
		foreach ($dayData as $data) {
		
			$dd=\DateTime::createFromFormat('Y-m-d', $year."-".$month."-".(++$idDay));
			//$dd = mktime(0, 0, 0,$month, ++$idDay, $year);
			$day=$dd->format("w");
			
			
			//$day= date("w",$dd);
			$dayType = ($day >= 1 && $day <=5)?"w":"h";
			//print_r ($dayType);
			$tempData =  $this->dayPeriodAverage [$dayType];
			
			for ($i=0 ; $i<4 ; $i++){
				$this->dayPeriodAverage [$dayType][$i] = ($data[$i] + $tempData[$i])/2;
			}
		}
		
		//dump("dayPeriodAverage");
		//dump($this->dayPeriodAverage);
	}
	
	/**
	* 
	* @param unknown $priceContainer
	*/
	public function Evaluate ()
	{
  
		$period = "NA";
		$maxDay = 0;
		$dayData = array();
		//$now = mktime();
		//$hour 	= date( "h", $now);//returns day of the week 0 Sunday -> 6 Saturday
		//$day 	= date( "w", $now);//returns day of the week 0 Sunday -> 6 Saturday
		//$month 	= date( "m", $now);//returns month of the year 01 January -> 12 December
		
		$now = $this->pricesContainer->getStartDate(); 
		
		//dump(" -- Now -- ");
		//dump($now);
		
		//$hour 	= $now->format('h');//returns hour of the day
		$mday 	= \DateTime::createFromFormat("Y-m-d", $now)->format('d');//returns day of the week 0 Sunday -> 6 Saturday
		$day 	= \DateTime::createFromFormat("Y-m-d", $now)->format('w');//returns day of the week 0 Sunday -> 6 Saturday
		$month 	= \DateTime::createFromFormat("Y-m-d", $now)->format('m');//returns month of the year 01 January -> 12 December
		$year 	= \DateTime::createFromFormat("Y-m-d", $now)->format('Y');//returns month of the year 01 January -> 12 December
					   
		if ($month==1 || $month==3 || $month==5 ||$month==7 || $month==8 || $month==10 || $month==12)        
		  $maxDay = 31;
		else if ($month==2 )   
		  $maxDay = 28;
		else
		  $maxDay = 30;
    
    
		for ($idxDay= 0; $idxDay < $maxDay; $idxDay++){
			$dayType = ($day >= 1 && $day <=5)?"w":"h";
			//if ($month >= 3 && $month <=9 ){ //Summer
				
				$periods = $this->pricesContainer->getTariffType()->getPeriodsByType($dayType);
			
				for ($i = 0; $i < count($periods)-1; ++$i) {
					$idx=0;
					$val=0;
					for ($k = $periods[$i]; $k < $periods[$i+1]; $k++,$idx++) {
						$val+= $this->pricesContainer->getDailyPrices()->getPrice($k + $idxDay*24);
					}
					$dayData[$idxDay][]=$val/$idx;
				}
			//}
			//else{ //Winter
			//} 
			$newDate = $year."-".$month."-".$mday++;
		  
			$now=\DateTime::createFromFormat("Y-m-d" ,$newDate); //$now = new DateTime($newDate);		  
		  
			$day = $now->format('w');
		}
		
     
		//dump($dayData);
		$this->CalculateAveragePerDayType ($dayData);
	}

}//End of class
?>