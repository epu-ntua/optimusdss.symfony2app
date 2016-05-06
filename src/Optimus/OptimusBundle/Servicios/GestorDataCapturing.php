<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Servicios\GestorOntologia;

class GestorDataCapturing { 
	
	protected $ontologia;
 
    protected $em;
 
    public function __construct(GestorOntologia $ontologia , EntityManager $em)
    {
        $this->ontologia=$ontologia;
		$this->em=$em;
    }
	
	/* ----- DATA HISTORIC & FORECAST -----*/
	public function getDataFromDate($actualDate='', $daySelected='', $nameVariable='', $type='variable', $idBuilding )
	{		
		$variablesActivas=$this->getVariablesActives($nameVariable, $idBuilding);
		$aFinal=$this->getDataVariables($variablesActivas, $actualDate, $daySelected, $type, $idBuilding);
		
		return $aFinal;
	}  
	
	//Ahora los sensore activos en la configuración
	private function getVariablesActives($nameVariable='', $idBuilding)
	{		
		if($nameVariable=='')
			$sensors=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array('display'=>'yes', "fk_Building"=>$idBuilding));	
		else 
			$sensors=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array('name'=>$nameVariable, "fk_Building"=>$idBuilding));	
		return $sensors;
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
	
	//Gestiona si son sólo datos históricos o predictivos
	private function getDataVariables($variablesActivas, $actualDate, $daySelected, $type, $idBuilding)
	{		
		$dayFinal=\DateTime::createFromFormat('Y-m-d H:i:s', $daySelected)->modify(" +6 day")->format("Y-m-d H:i:s");
		
		if($dayFinal < $actualDate)
		{			
			//Only historic data
			$aDataHistoric=$this->getDataVariableHistorical($variablesActivas, $daySelected, $dayFinal);
			$aDataHistoricFinal=$this->getArrayFinalHistoricalVariables($aDataHistoric);
			$aFinal=$aDataHistoricFinal;
		}elseif($daySelected <= $actualDate)
		{			
			//data historic
			$aDataHistoric=$this->getDataVariableHistorical($variablesActivas, $daySelected, $actualDate);
			$aDataHistoricFinal=$this->getArrayFinalHistoricalVariables($aDataHistoric);
			
			if($type!='variable') $aDataHistoricFinal=$this->getArrayFinalHistoricalDate($aDataHistoricFinal, $daySelected, $actualDate);
						
			//data prediction
			$aDataPrediction=$this->getDataVariablePrediction($variablesActivas, $actualDate, $dayFinal, $idBuilding);
			$aDataPredictionFinal=$this->getArrayFinalPredictionVariables($aDataPrediction);
			
			$aFinal=array_merge_recursive($aDataHistoricFinal, $aDataPredictionFinal);
			
		}elseif($daySelected > $actualDate)
		{			
			//only prediction
			$aDataPrediction=$this->getDataVariablePrediction($variablesActivas, $daySelected, $dayFinal, $idBuilding);
			$aDataPredictionFinal=$this->getArrayFinalPredictionVariables($aDataPrediction);
			$aFinal=$aDataPredictionFinal;
		}
		return $aFinal;
	}
	
	//Devuelve los datos históricos de la ontología
	private function getDataVariableHistorical($variablesActivas, $from, $to)
	{
		$aDataVariable=array();
		foreach($variablesActivas as $variable)
		{			
			//si historico
			$aDataHistoric=$this->ontologia->getDataParameterFromOntology($from, $to, $variable->getUrl());
			$aDataVariable[]=array("name"=>$variable->getName(), "values"=>$aDataHistoric, "color"=>$variable->getColor());	
		}		
		return $aDataVariable;
	}
	
	//Devuelve los datos predictivos
	private function getDataVariablePrediction($variablesActivas, $from, $to, $idBuilding)
	{
		$aDataPrediction=array();
		$aDays=$this->getDaysFromDate($from, $to);
		//Para cada día miras si hay predicción
		for($i=0; $i<count($aDays); $i++)
		{			
			$aDataVariablePrediction=array();
			$qPrediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($aDays[$i], $idBuilding);
			//Para cada variable getValues de la predicción
			if(!empty($qPrediction))
			{
				$idPrediction=$qPrediction[0]->getId();				
				
				//Para cada variable obtner los valores del dia
				foreach($variablesActivas as $variable)
				{					
					//get registros
					$aDataRegisterPrediction = $this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate($variable->getId(), $idPrediction, $aDays[$i]);
					
					$aValues=array();
					foreach($aDataRegisterPrediction as $register)
					{
						$aValues[]=array("value"=>$register->getValue(), "date"=>$register->getDate()->format("Y-m-d H:i:s"));
					}
					
					$aDataVariablePrediction[]=array("name"=>$variable->getName(), "values"=>$aValues, "color"=>$variable->getColor());
				}				
			}
			$aDataPrediction[]=array("day"=>$aDays[$i], "variables"=>$aDataVariablePrediction);
		}		
		return $aDataPrediction;
		
	}
	
	//Procesa los datos históricos devueltos por la ontología
	private function getArrayFinalHistoricalVariables($aDataHistoric)
	{		
		$aDataFinal=array();
		
		for($h=0; $h<count($aDataHistoric); $h++)
		{				
			$nameVariable=$aDataHistoric[$h]['name'];
			$color=$aDataHistoric[$h]['color'];
			
			$aValues=array();
			if(isset($aDataHistoric[$h]['values']))
			{
				foreach($aDataHistoric[$h]['values'] as $value)
				{				
					$date=str_replace("T", " ", $value['datetime']);
					$dateFinal=substr($date, 0, -1);	
					$aValues[]=array("value"=>$value['value'], "date"=>$dateFinal);				
				}
			}
			$aDataFinal[]=array("name"=>$nameVariable, "values"=>$aValues, "type"=>'historical', "color"=>$color);
		}
			
		return $aDataFinal;
	}

	//Procesa los datos predictivos 
	private function getArrayFinalPredictionVariables($aDataPrediction)
	{
		$aDataFinal=array();
		$aNamesVariables=array();
		$aVariables=array();
		for($i=0; $i<count($aDataPrediction); $i++)
		{				
			//Para cada variable getValues de la predicción
			if(!empty($aDataPrediction[$i]['variables']))
			{				
				//Para cada variable obtner los valores del dia
				foreach($aDataPrediction[$i]['variables'] as $variable)
				{				
					if(!in_array($variable['name'], $aNamesVariables))
					{
						$aNamesVariables[]=$variable['name'];
						$aVariables[$variable['name']]=array();
					}					
					$aValues=array();
					foreach($variable['values'] as $register)
					{
						$aVariables[$variable['name']][]=array("value"=>$register['value'], "date"=>$register['date']);
					}					
				}				
			}		
		}
		
		foreach($aVariables as $name=>$values)
		{
			$aDataFinal[]=array("name"=>$name, "values"=>$values, "type"=>'prediction', "color"=>"#ff0000");
		}
		
		return $aDataFinal;
	}

	//Procesa los datos históricos por días (no se utiliza)
	private function getArrayFinalHistoricalDate($aDataHistoric, $from, $to)
	{
		$aDays=$this->getDaysFromDate($from, $to);
		$aDataFinal=array();
		$numDays=count($aDays);
		for ($i=0; $i< $numDays; $i++)
		{	
			$aVariable=array();
			//Primero miramos si hay datos historicos
			for($h=0; $h<count($aDataHistoric); $h++)
			{				
				$nameVariable=$aDataHistoric[$h]['name'];
				
				$aValues=array();
				foreach($aDataHistoric[$h]['values'] as $value)
				{					
					if(strtotime($value['date'])>=strtotime($aDays[$i]) and strtotime($value['date'])<=strtotime($aDays[$i]." +1 day"))
					{
						$aValues[]=$value;
					}
				}
				
				$aVariable[]=array("name"=>$nameVariable, "values"=>$aValues);
			}
			$aDataFinal[]=array("day"=>$aDays[$i], "variables"=>$aVariable);
		}
			
		return $aDataFinal;
	}
	
	/* ----- DATA ACTION PLAN -----*/
	
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
			//dump($outputDay);
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
		//dump($aDataActionPlan);
		
		return $aDataActionPlan;
	}
	

}

?>