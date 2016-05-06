<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APCalculation;
use Optimus\OptimusBundle\Entity\APPVOutput;
use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class ServiceAPPV {
 
    protected $em;
 
    public function __construct(EntityManager $em)
    {       
		$this->em=$em;
    }
	
	public function insertPVOutput($actionPlan, $from, $calculation, $idBuilding)
	{		
		foreach($actionPlan['sensors'] as $sensor)
		{			
			for($i=0; $i<7; $i++)
			{
				if($i>0)
				{
					$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
					$dateWeek=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
				}else $dateWeek=$from;
				
				$prediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($dateWeek, $idBuilding);	//prediction
				//get registers prediction
				
				if(!empty($prediction))
				{
					$registerPrediction=$this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate($sensor, $prediction[0]->getId(), $dateWeek);
					
				}else $registerPrediction=array();
				
				foreach($registerPrediction as $register)
				{
					$output = new APPVOutput();
					$output->setHour($register->getDate());
					$output->setEnergyPrice(1.5);
					$output->setEnergyProduction($register->getValue());
					$output->setFkApCalculation($calculation);
				 
					
					$this->em->persist($output);
					$this->em->flush();
				}									
				
				//insert strategy & status of day			
				$date=explode(" ", $dateWeek);
				$day=\DateTime::createFromFormat('Y-m-d', $date[0]);
				
				$outputDay = new APPVOutputDay();
				$outputDay->setDate($day);
				$outputDay->setStrategy('Financial');
				$outputDay->setStatus(0);
				$outputDay->setFkApCalculation($calculation);
			 
				
				$this->em->persist($outputDay);
				$this->em->flush();
			}				
		}
	} 
	
	//Devuelve los datos para la tabla de Action plan PV
	public function getDataPVActionPlan($from, $idActionPlan)
	{
		$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
		$finalDay=$actDay->modify(" +6 day")->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($from, $finalDay);
		
		$aDataActionPlan=array();
		$totalWeek=0;
		$numDays=count($aDays);
		for($i=0; $i < $numDays; $i++)
		{
			$aDataCalculation=array();
			$consume=$difference=$averageDay=$production=$purchase=$selling=$dailyAcumulated=0;
			$idStatusDay=0;
			$idCalculation=0;
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			
			if(!empty($qCalculation)) //hay calculo para el dia
			{
				$idCalculation=$qCalculation[0]->getId();
													
				//get outputs
				$aRegisterCalculation = $this->em->getRepository('OptimusOptimusBundle:APPVOutput')->findResgisterOutputsByDate($idCalculation, $aDays[$i]);
					
				$numRegisters=0;				
				foreach($aRegisterCalculation as $register)
				{
					$aDataCalculation[]=array("energy_price"=>$register->getEnergyPrice(), "energy_production"=>$register->getEnergyProduction(), "date"=>$register->getHour()->format("Y-m-d H:i:s"));
					
					$averageDay=$averageDay+$register->getEnergyProduction();
					$production=$production+$register->getEnergyProduction();
					$consume=$consume+($register->getEnergyProduction()*2/200); 
					$difference=$difference+($register->getEnergyProduction()-($register->getEnergyProduction()*2/200));
					$purchase=$register->getEnergyPrice();
					$selling=$register->getEnergyPrice();
					$dailyAcumulated=$dailyAcumulated + $register->getEnergyProduction();
					
					$numRegisters++;	
				}
				if($averageDay>0) $averageDay=$averageDay/$numRegisters;
			}
			
			$dayLookFor=\DateTime::createFromFormat("Y-m-d H:i:s", $aDays[$i]);
			$dayFinal=explode(" ", $aDays[$i]);
			
			//dump($dayFinal);
			//add idOutputDay
			$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $dayFinal[0]);
			dump($outputDay);
			if($outputDay)
			{
				$idOutputDay=$outputDay[0]->getId();
				$status=$outputDay[0]->getStatus();
				$strategy=$outputDay[0]->getStrategy();
			}else{
				$idOutputDay=0;
				$status=0;
				$strategy="";
			}
			
			$aDataActionPlan[]=array("day"=>$aDays[$i], "calculation"=>$aDataCalculation, "averageDay"=>$averageDay, "production"=>$production, "consume"=>$consume, "difference"=>$difference, "purchase"=>$purchase, "selling"=>$selling, "dailyAcumulated"=>$dailyAcumulated, "idCalculation"=>$idCalculation, "idOutputDay"=>$idOutputDay, "status"=>$status, "strategy"=>$strategy); //change idStatus
		}
		dump($aDataActionPlan);
		
		return $aDataActionPlan;
	}
	
	//Devuelve un array con todos los días entre 2 fechas
	private function getDaysFromDate($from, $to)
	{
		$aDays=array();
		$aDays[0]=$from;
		for($i=1; $i<7; $i++)
		{
			$actDay=\DateTime::createFromFormat('Y-m-d H:i:s', $from);
			$act=$actDay->modify(" +".$i." day")->format("Y-m-d H:i:s");
			if(($act) < $to)
				$aDays[$i]=$act;
			else break;
		}		
		$aDays[$i]=$to;		
		
		return $aDays;
	}
	
}
?>