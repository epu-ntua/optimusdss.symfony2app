<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;

class ServiceDashboard {
	 
    protected $em;
	protected $ontologia;	
	
    public function __construct(EntityManager $em)
    {
		$this->em=$em;		
    }
	
	public function getDaysFromDate($from, $to)
	{
		$aDays=array();
		$aDays[0]=$from;
		
		$date1=explode(" ",$from);
		$date2=explode(" ",$to);
		
		$dateStart= new \DateTime($date1[0]);
		$dateEnd= new \DateTime($date2[0]);
		$dateDiff = $dateStart->diff($dateEnd);
		$maxDays=$dateDiff->days;
			
			
		for($i=1; $i<$maxDays; $i++)
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
	
	public function getDeclinesActiveUnknows($building, $from, $to)
	{			
		$days=$this->getDaysFromDate($from, $to);
		
		$numUnknows=$numAccepts=$numDeclines=0;	
		
		$allValues=array();
		
		$actionPlans=$this->em->getRepository('OptimusOptimusBundle:ActionPlans')->findActionPlansWithSensors($building->getId());
		foreach($actionPlans as $aPlan)
		{			
			foreach($days as $day)
			{
				//dump($day);
				
				$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($day, $aPlan->getId());
				
				$status=null;
				if($qCalculation)
				{
					//dump($qCalculation[0]->getId());
					
					$idCalculation=$qCalculation[0]->getId();
					$outputDay =array();
					switch($aPlan->getType())
					{
						case 4: 
							$dForPVM=explode(" ",$day);
							$dForPVM=$dForPVM[0];
							$outputDay = $this->em->getRepository('OptimusOptimusBundle:APSwitchOutputDay')->findOutputByDay($idCalculation, $dForPVM); //
							
							//dump($outputDay);
							break;
						case 5:
							$dForPVM=explode(" ",$day);
							$dForPVM=$dForPVM[0];
							$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVMOutputDay')->findOutputByDay($idCalculation, $dForPVM); //
							//dump($outputDay);
							break;
						case 6:
							$dForPVM=explode(" ",$day);
							$dForPVM=$dForPVM[0];
							$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $dForPVM); //
							//dump($outputDay);
							break;					
					}
					
					$status = false;
					
					if($outputDay)		$status=$outputDay[0]->getStatus();		
					
					//dump($day." -> ". $outputDay[0]->getStatus());
					//if($status != false) {
						if($status===0)			$numUnknows++;
						elseif($status==1)		$numAccepts++;
						elseif($status==2)		$numDeclines++;
						
						//dump($day." -> ". $status);
						
					//}
				} else {
					//No hay calculo, suponemos que son uknowns.
					$numUnknows++;
					$status = 0;
				}
				$allValues[]=array("date"=>$day, "status"=>$status);
			}
		}
		
		$aData=array("declines"=>$numDeclines, "accepts"=>$numAccepts, "unknows"=>$numUnknows, "allValues"=>$allValues);
		
		return $aData;
		
	}
	
}
?>