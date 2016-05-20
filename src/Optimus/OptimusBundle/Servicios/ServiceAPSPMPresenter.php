<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class ServiceAPSPMPresenter {
 
    protected $em;
	protected $apadaptative;
    protected $aptcv;
 
    public function __construct(EntityManager $em, ServiceAPAdaptative $apadaptative, ServiceAPTCV $aptcv)
    {       
		$this->em=$em;
		$this->apadaptative=$apadaptative;
		$this->aptcv=$aptcv;
    }
	
	//Get Status week 
	public function getStatusWeek($idActionPlan, $startDate, $endDate)
	{
		//$initDay=$startDate." 00:00:00";
		$initDay=\DateTime::createFromFormat('Y-m-d H:i:s', $startDate." 00:00:00")->modify("+0 day")->format("Y-m-d H:i:s");
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
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSPMOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{
					$aStatusWeek[]=array('status'=>$outputDay[0]->getStatus(), 'idOutputDay'=>$outputDay[0]->getId());//statusDay
					
				}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
				
			}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
		}
		//dump($aStatusWeek);
		return $aStatusWeek;
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
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSPMOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
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
				$aDataActionPlan[]=array("status"=>"#ffff00", "date"=>$aDays[$i]);
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
	
	// Returns an array of every day between two dates:
	private function getDaysFromDate($from, $to)
	{
		$aDays=array();
		$aDays[0]=$from;
		for($i=1; $i<7; $i++)
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

}
?>