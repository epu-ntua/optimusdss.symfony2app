<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\CurrentStatus;

class ServiceTracker {
	 
    protected $em;	
    protected $rtime;	
	
    public function __construct(EntityManager $em, ServiceBuildingSensorsRTime $rtime)
    {
		$this->em=$em;		
		$this->rtime=$rtime;		
    }
	
	public function currentStatusTracker()
	{	
		$dateActual=new \DateTime();
			
		//1. Baseline user form y /12
		//2. Calcular fechas des de 1 de enero de este año hasta el ultimo dia del mes anterior
		//3. Pedir para los 4 indicadores los datos para estas fechas RTime    
		//4. Sumar valores para total y dividir entre el num. de meses (pto 2)
		//5. 100 -(Dividir pto4/pto1 *100) (valor entre -100 y 100)--> guardar en BD 
		//6. En la vista la barra si es valor negativo --> a 0
		
		//////
		//1.
		/////
		$baseEconsumption=$baseCo2=$baseEcost=$baseREUse=0;
		
		$valuesTracker=$this->em->getRepository('OptimusOptimusBundle:Tracker')->findById(1);
		if($valuesTracker)
		{
			dump($valuesTracker[0]->getBaselineEConsumption());
			$baseEconsumption=$valuesTracker[0]->getBaselineEConsumption()/12;
			$baseCo2=$valuesTracker[0]->getBaselineCO2()/12;
			$baseEcost=$valuesTracker[0]->getBaselineECost()/12;
			$baseREUse=$valuesTracker[0]->getBaselineREUse()/12;
		}
		
		//////
		//2.
		/////
		$lastMonth=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("first day of last month")->format("Y-m-d")." 00:00:00";
		$finalDayLastMonth=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("last day of last month");//->format("Y-m-d")." 00:00:00";
		$firstDayYear=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"))->modify("first day of January this year");//->format("Y-m-d")." 00:00:00";
		$diffMonths=$firstDayYear->diff($dateActual);
		$numMonths=$diffMonths->m;
		$numDays=$diffMonths->days;
		
		$initDay=$firstDayYear->format("Y-m-d")." 00:00:00";
		$lastDay=$finalDayLastMonth->format("Y-m-d")." 00:00:00";
		$actualDay=$dateActual->format("Y-m-d")." 00:00:00";
		
		dump('fechas RTime');
		dump($actualDay);		
		dump($initDay);
		dump($lastDay);
		dump($numDays);
		dump('--------------');
		
		//////
		//3.
		/////
		$energyCost=$co2=$energyConsumption=$pREnergy=0;
		
		$buildings=$this->em->getRepository('OptimusOptimusBundle:Building')->findAll();
		
		if($buildings)
		{
			foreach($buildings as $building)
			{
				$dataRTime=$this->rtime->getRTTime($actualDay, $initDay, $lastDay, $building->getId());
				dump($dataRTime);
				
				$energyCost+=$dataRTime['Energy cost'];
				$co2+=$dataRTime['CO2'];
				$energyConsumption+=$dataRTime['Energy consumption'];
				$pREnergy+=$dataRTime['Produced renewable energy'];				
			}	
				
			//////
			//4.1
			/////
			
			
			if($energyCost!=0) 				$energyCost=$energyCost/count($buildings);			
			if($co2!=0) 					$co2=$co2/count($buildings);
			if($energyConsumption!=0)		$energyConsumption=$energyConsumption/count($buildings);
			if($pREnergy!=0)				$pREnergy=$pREnergy/count($buildings);
			
			dump("---- Media total ---");
			dump($energyCost);
			dump($co2);
			dump($energyConsumption);
			dump($pREnergy);
		}		
		
		//////
		//4.2
		/////		
		
		if($energyCost!=0) 				$energyCost=$energyCost/12;			
		if($co2!=0) 					$co2=$co2/12;
		if($energyConsumption!=0)		$energyConsumption=$energyConsumption/12;
		if($pREnergy!=0)				$pREnergy=$pREnergy/12;
		
		dump("---- Media por mes ---");
		dump($energyCost);
		dump($co2);
		dump($energyConsumption);
		dump($pREnergy);
		
		//////
		//5.
		/////
		if($energyCost!=0 and $baseEcost!=0) 					$statusECost=100 -($energyCost*100/$baseEcost);
		else 													$statusECost=$energyCost;
		
		if($co2!=0 and $baseCo2!=0) 							$statusCO2=100 -($co2*100/$baseCo2 );
		else													$statusCO2=$co2;
		
		if($energyConsumption!=0 and $baseEconsumption!=0)		$statusEConsumption=100 -($energyConsumption*100/$baseEconsumption );
		else													$statusEConsumption=$energyConsumption;
		
		if($pREnergy!=0 and $baseREUse!=0)						$statusREUse=100 -($pREnergy*100/$baseREUse );
		else													$statusREUse=$pREnergy;
		
		dump("---- % ---");
		dump($statusECost);
		dump($statusCO2);
		dump($statusEConsumption);
		dump($statusREUse);
		
		$currentStatus=new CurrentStatus();
		$currentStatus->setDate($dateActual);
		$currentStatus->setStatusEConsumption($statusEConsumption);			
		$currentStatus->setStatusCO2($statusCO2);			
		$currentStatus->setStatusECost($statusECost);			
		$currentStatus->setStatusREUse($statusREUse);			
		$this->em->persist($currentStatus);
		$this->em->flush();
		
	}
}