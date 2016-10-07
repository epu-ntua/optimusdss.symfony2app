<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Optimus\OptimusBundle\Servicios\GestorOntologia;

class ServiceAPPHPresenter
{
	// Variables members --------------------------------------------------------------------------
		
    protected $em;
	protected $ontologia;
	protected $maDataIntervalDay;
	
	// Constructors -------------------------------------------------------------------------------
	
	public function __construct(EntityManager $em,
								ServiceOntologia $ontologia)
    {
		// Params: htdocs\optimus\app\config\config.yml
		//         htdocs\optimus\src\Optimus\OptimusBundle\Resources\config\services.yml
	
        $this->ontologia=$ontologia;
		$this->em=$em;		
	}
	
	// Methods ------------------------------------------------------------------------------------
	
	public function getDataValues($aoDaySelectedOnTheCalendar, $idActionPlan)
	{
		$loCurrentDate=\DateTime::createFromFormat('Y-m-d H:i:s', $aoDaySelectedOnTheCalendar);	
		
		// Get the current day -> the "first" one and the "last" one for calculating:
		$loInitDay=$loCurrentDate->modify("+0 day")->format("Y-m-d H:i:s");
		$loFinalDay=$loCurrentDate->modify("+6 day")->format("Y-m-d H:i:s");
		
		$loLstDays=$this->getDaysFromDate($loInitDay, $loFinalDay);	// Returns an array of every day between two dates	
		$liNumDays=count($loLstDays);								// Number of days: should be 7 (days)	
		$aDataActionPlan=array();
		$maxIntervals=1;
					
		// for each day from starting date to 6 more days ...
		for($i=0; $i < $liNumDays; $i++)
		{
			// Variables:
			$aDataCalculation=array();
			
			$idStatusDay=0;			
			$idCalculation=0;		
			$idOutputDay=0;
			$status=0;
			$strategy="no";		
			$dateCalculationActual='';
			$dateCalculationBefore='';
			//dump($loLstDays[$i]);
			
			
			// Get the last calculation for the current day:
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($loLstDays[$i], $idActionPlan);
			//$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findBy(array("fk_actionplan"=>$idActionPlan, "startingDate"=>\DateTime::createFromFormat("Y-m-d H:i:s", "2015-02-02 00:00:00")));
			
			$dayLookFor=\DateTime::createFromFormat("Y-m-d H:i:s", $loLstDays[$i]);
			$lsAbbreviatedDay=\DateTime::createFromFormat("Y-m-d H:i:s", $loLstDays[$i]);
			$lsAbbreviatedDayFinal = $lsAbbreviatedDay;
			$lsAbbreviatedDayFinal = $lsAbbreviatedDayFinal->format('d-m');
			$nameAbbreviatedDay = $lsAbbreviatedDay->format('D');
			$dayFinal=explode(" ", $loLstDays[$i])[0];
			
			//dump($dayFinal);
						
			// if there is "calculation data" for this day...
			if(!empty($qCalculation)) 
			{
				try 
				{
					$idCalculation=$qCalculation[0]->getId();
					$dateCalculationActual=$qCalculation[0]->getDateCreation()->format('Y-m-d H:i:s');
					
					$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutputDay')->findOutputByDay($idCalculation, $dayFinal); //
					if($outputDay)
					{
						$idOutputDay=$outputDay[0]->getId();		// 1, 2....
						$status=$outputDay[0]->getStatus();			// 0=Unknown, 1=Accepted, 2=Declined 
						//$strategy=$outputDay[0]->getStrategy();		// Green, Finance, Intermediate
						//$strategy = strtolower($strategy);
					}
					
				} 
				catch (Exception $e) 
				{
					//echo 'Excepción capturada: ',  $e->getMessage(), "\n";
					continue;
				}			
			
				// 1. Get PV information: Prices and Production				
				
				//Comprobamos si hay un cálculo anterior para el mismo día
				$before=false;				
				if(isset($qCalculation[1]))
				{
					$idCalculationBefore=$qCalculation[1]->getId();
					$aRegisterCalculationBefore = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutput')->findResgisterOutputsByDate($idCalculationBefore, $loLstDays[$i]);
					
					if($aRegisterCalculationBefore)
					{
						$before=true;
						$dateCalculationBefore=$qCalculation[1]->getDateCreation()->format('Y-m-d H:i:s');
					}
					
				}
				
				$aRegisterCalculationActual = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutput')->findResgisterOutputsByDate($idCalculation, $loLstDays[$i]);
			 
				//dump($aRegisterCalculationActual);
				
				// 2. For each register -> (HOUR)
				$numRegisters=0;
				$hourlyCumulativeIncomePV=0;
				$this->maDataIntervalDay=array();
				$suggestion=-1;						
				
				// for each hour... (0..23)
				foreach($aRegisterCalculationActual as $register)
				{	
					$diferentStart=$diferentStop=false;
					$numStartBefore=$numStopBefore=0;
					if($before==true) //hay registros de un cálculo anterior
					{						
						foreach($aRegisterCalculationBefore as $registerBefore)
						{
							if(($register->getDay()== $registerBefore->getDay()) and ($register->getFkBuildingPartitioning()->getId()==$registerBefore->getFkBuildingPartitioning()->getId()))
							{
								if($register->getStart()!= $registerBefore->getStart())
								{
									$diferentStart=true;
									$numStartBefore=$registerBefore->getStart();
								}
								
								if($register->getStop()!= $registerBefore->getStop())
								{
									$diferentStop=true;
									$numStopBefore=$registerBefore->getStop();
								}
							}
						}
					}
					if($diferentStart==true)	$startDifferent="different";
					else $startDifferent="";
					
					if($diferentStop==true)	$stopDifferent="different";
					else $stopDifferent="";
					
					$aDataCalculation[]=array("date"=>$register->getDay(), "start"=>$register->getStart(), "stop"=>$register->getStop(), "partition"=>$register->getFkBuildingPartitioning()->getId(), "setpoint"=>$register->getSetpoint(), "startDifferent"=>$startDifferent, "stopDifferent"=>$stopDifferent, "numStartBefore"=>$numStartBefore, "numStopBefore"=>$numStopBefore );
				}				
			}
	
			//dump($aDataActionPlan);
			//dump("strategy: ".$strategy);
			
			// Insert information of the current day into a global array (7 days):
			$aDataActionPlan[]=array("day"=>explode(" ", $loLstDays[$i])[0], 									 
									 "idCalculation"=>$idCalculation,
									 "dateCalculationActual"=>$dateCalculationActual,
									 "dateCalculationBefore"=>$dateCalculationBefore,
									 "idOutputDay"=>$idOutputDay,
									 "status"=>$status,
									 "nameAbbreviatedDay"=>$nameAbbreviatedDay,
									 "abbreviatedDay"=>$lsAbbreviatedDayFinal,
									 "interval"=>$this->maDataIntervalDay,
									 "maxIntervals"=>$maxIntervals,
									 "dataCalculation"=>$aDataCalculation,
									 "strategy"=>$strategy); //change idStatus			
		}		
		//dump($aDataActionPlan);
		
		return $aDataActionPlan;
	}

	// Detect and add new interval limits of the day in a list
	private function checkNewInterval($oldSuggestion, $newSuggestion, $hour)
	{
		//dump($oldSuggestion." ".$newSuggestion);
		
		if($oldSuggestion != $newSuggestion)
		{
			$type = 0;
			foreach($this->maDataIntervalDay as $loIntervalDay)
			{
				if($loIntervalDay['type'] == $newSuggestion){
					$type++; 
				}				
			}
			$this->maDataIntervalDay[]=array("hour"=>$hour, "type"=>$newSuggestion, "groupView"=>$type);
		}		
		return $newSuggestion;
	}
	
	// Returns an array of every day between two dates:
	private function getDaysFromDate($from, $to)
	{
		$dias	= (strtotime($from)-strtotime($to))/86400;
		$dias 	= abs($dias); $dias = floor($dias);

		$aDays=array();
		$aDays[0]=$from;
		for($i=1; $i<$dias; $i++)
		{
			$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
			$act=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
			if(($act) < $to){
				$aDays[$i]=$act;
			} else {
				break;
			}
		}		
		$aDays[$i]=$to;		
		
		return $aDays;
	}

	//GET Status & colors action plan	
	public function getTrafficLight($idActionPlan, $dateActual, $startDate, $endDate)
	{
		
		$initDay=$startDate->format("Y-m-d H:i:s");
		//$finalDay=$endDate->modify('-2 day')->format("Y-m-d H:i:s");
		$finalDay=$endDate->format("Y-m-d H:i:s");
		$actDay=$dateActual->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($initDay, $finalDay);		
		$numDays=count($aDays);								
		$aDataActionPlan=array();
		$aFinalValues=array();
				
		for($i=0; $i < $numDays; $i++)
		{
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			$currentDayFormat=explode(" ", $aDays[$i])[0];
			
			
			if($qCalculation != null)
			{
				$idCalculation=$qCalculation[0]->getId();			
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{					
					if($aDays[$i] < $actDay)
					{
						$status=$outputDay[0]->getStatus();
						if($status==0) 		$color="#ffff00";
						elseif($status==1)	$color="#00ff00";
						elseif($status==2)	$color="#ff0000";
						$aDataActionPlan[]=array("status"=>$color, "date"=>$aDays[$i]);			// 0=Unknown, 1=Accepted, 2=Declined 
					}elseif($aDays[$i] >= $actDay)
					{
						$status=$outputDay[0]->getStatus();
						if($status==0) 		$color="#cccccc";
						elseif($status==1)	$color="#00ff00";
						elseif($status==2)	$color="#ff0000";
						
						$aDataActionPlan[]=array("status"=>$color, "date"=>$aDays[$i]);
					}
				}else{					
					$aDataActionPlan[]=array("status"=>"#ffff00", "date"=>$aDays[$i]);
				}
			}else{				
				$aDataActionPlan[]=array("status"=>"#ffff000", "date"=>$aDays[$i]);
			}
		}
			
		$numUnk=$this->calculateUnknowns($aDataActionPlan, $dateActual);
		
		//if($numUnk == 0) 		$strStatus=0;
		if($numUnk >= 1)		$strStatus=1;
		else					$strStatus=2;
		
		$aFinalValues[]=array("aOutputActionPlan"=>$aDataActionPlan, "status"=>$strStatus);
		
		return $aFinalValues;
		
	}
	
	//Get number of unknows 
	private function calculateUnknowns($aDataActionPlan, $dateActual)
	{
		$actDay=$dateActual->format("Y-m-d");
		$numUnk=0;
		foreach($aDataActionPlan as $dayActionPlan)
		{
			$currentDay=explode(" ", $dayActionPlan['date'])[0];
			if($currentDay >= $actDay and $dayActionPlan['status']=="#cccccc")		$numUnk++;
			
		}
		
		return $numUnk;
	}

	//Get Status week 
	public function getStatusWeek($idActionPlan, $startDate, $endDate)
	{
		$initDay=$startDate." 00:00:00";
		$finalDay=\DateTime::createFromFormat('Y-m-d H:i:s', $endDate." 00:00:00")->modify("+1 day")->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($initDay, $finalDay);		
		$numDays=count($aDays);
		$aStatusWeek=array();

		for($i=0; $i < $numDays; $i++)
		{
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			$currentDayFormat=explode(" ", $aDays[$i])[0];
			
			
			if($qCalculation != null)
			{
				$idCalculation=$qCalculation[0]->getId();			
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{
					$aStatusWeek[]=array('status'=>$outputDay[0]->getStatus(), 'idOutputDay'=>$outputDay[0]->getId());
					
				}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
				
			}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
		}
		
		return $aStatusWeek;
	}
}
?>

 