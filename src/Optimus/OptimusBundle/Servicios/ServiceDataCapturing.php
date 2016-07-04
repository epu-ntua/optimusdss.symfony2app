<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Optimus\OptimusBundle\Servicios\GestorOntologia;
use Optimus\OptimusBundle\Servicios\ServiceAPPVMPresenter;

class ServiceDataCapturing 
{ 
	// Members ------------------------------------------------------------------------------------
	
	// Static members:	
	private static $historical_key = "historical";
	private static $prediction_key = "prediction";
	
	private static $sensor_energyProduction_name = "Energy Production";		// double values: historical and predicted
	private static $sensor_solarRadiation_name = "Solar Radiation";			// double values: historical and predicted
	
	// Variables members:
	protected $em;
	protected $ontologia;
    protected $energy_demand_threshold_percentage;
	protected $daily_penalty_energy_percentage;
		
	public function getEnergyProductionName(){return self::$sensor_energyProduction_name;}
	public function getSolarRadiationName(){return self::$sensor_solarRadiation_name;}
	
	public $weekSoldProducedAcumulated = 0;
    public $weekEmissionsAcumulated = 0;
 
	// Constructors -------------------------------------------------------------------------------
	
    public function __construct(ServiceOntologia $ontologia, 
								EntityManager $em,								
								$energy_demand_threshold_percentage, 
								$daily_penalty_energy_percentage)
    {
		// Params: htdocs\optimus\app\config\config.yml
		//         htdocs\optimus\src\Optimus\OptimusBundle\Resources\config\services.yml
	
        $this->ontologia=$ontologia;
		$this->em=$em;		
		$this->energy_demand_threshold_percentage = $energy_demand_threshold_percentage;		
		$this->daily_penalty_energy_percentage = $daily_penalty_energy_percentage;		
    }
	
	// Methods ------------------------------------------------------------------------------------
	
	/* ----- DATA HISTORIC & FORECAST -----*/
	public function getDataFromDate($currentDate='', $from='', $finalDay='', $nameVariable='', $type='variable', $idBuilding )
	{		
		$variablesActivas=$this->getVariablesActives($nameVariable, $idBuilding);
		
		$aFinal=$this->getDataVariables($variablesActivas, $currentDate, $from, $finalDay, $type, $idBuilding);
        
  		return $aFinal;
	}  
	
	// Get data from active sensors defined in the configuration:
	private function getVariablesActives($nameVariable='', $idBuilding)
	{		
		if($nameVariable==''){
			$sensors=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array('display'=>'yes', "fk_Building"=>$idBuilding));	
		}elseif(is_array($nameVariable)) {
			$sensors=$this->em->getRepository('OptimusOptimusBundle:Sensor')->getSensorsFromArray($nameVariable);	
		} else {
			$sensors=$this->em->getRepository('OptimusOptimusBundle:Sensor')->findBy(array('name'=>$nameVariable, "fk_Building"=>$idBuilding));	
		}
		return $sensors;
	}

	// Returns an array of every day between two dates:
	public function getDaysFromDate($from, $to, $scope='')
	{
		$aDays=array();
		$aDays[0]=$from;
		
		if($scope!=''){
			$date1=explode(" ",$from);
			$date2=explode(" ",$to);
			
			$dateStart= new \DateTime($date1[0]);
			$dateEnd= new \DateTime($date2[0]);
			$dateDiff = $dateStart->diff($dateEnd);
			$maxDays=$dateDiff->days;
			
			//dump($maxDays);
		}else{
			$maxDays=7;
		}
		
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
	
	// Manage historical or predictive data:
	public function getDataVariables($variablesActivas, $actualDate, $daySelected, $finalDay, $type, $idBuilding)
	{		
		if($finalDay=='') $dayFinal=\DateTime::createFromFormat('Y-m-d H:i:s', $daySelected)->modify(" +7 day")->format("Y-m-d H:i:s");
		else $dayFinal=$finalDay;

		
		if($dayFinal < $actualDate)
		{	

			//Only historic data
			$aDataHistoric=$this->getDataVariableHistorical($variablesActivas, $daySelected, $dayFinal);
			$aDataHistoricFinal=$this->getArrayFinalHistoricalVariables($aDataHistoric);
			$aFinal=$aDataHistoricFinal;
			//dump($aFinal);
		}
		elseif($daySelected <= $actualDate)
		{	
			//data historic
			$aDataHistoric=$this->getDataVariableHistorical($variablesActivas, $daySelected, $actualDate);
			$aDataHistoricFinal=$this->getArrayFinalHistoricalVariables($aDataHistoric);
			
			if($type!='variable') {
				$aDataHistoricFinal=$this->getArrayFinalHistoricalDate($aDataHistoricFinal, $daySelected, $actualDate);
			}
						
			//data prediction
			$aDataPrediction=$this->getDataVariablePrediction($variablesActivas, $actualDate, $dayFinal, $idBuilding);
			$aDataPredictionFinal=$this->getArrayFinalPredictionVariables($aDataPrediction);
			
			
			$aFinal=array_merge_recursive($aDataHistoricFinal, $aDataPredictionFinal);			
		}
		elseif($daySelected > $actualDate)
		{			
		
			//only prediction
			$aDataPrediction=$this->getDataVariablePrediction($variablesActivas, $daySelected, $dayFinal, $idBuilding);
			$aDataPredictionFinal=$this->getArrayFinalPredictionVariables($aDataPrediction);
			$aFinal=$aDataPredictionFinal;
		}
		return $aFinal;
	}
	
	// Retrieve and returns the historical data from the triple data store:
	private function getDataVariableHistorical($variablesActivas, $from, $to)
	{
		$aDataVariable=array();
		foreach($variablesActivas as $variable)
		{			
			$aDataHistoric=$this->ontologia->getDataParameterFromOntology($from, $to, $variable->getUrl(), $variable->getAggregation());
			
			$aDataVariable[]=array(	"name"=>$variable->getName(), 
									"values"=>$aDataHistoric, 
									"color"=>$variable->getColor(),	
									"idSensor"=>$variable->getId(),	
									"units"=>$variable->getUnits());	
		}		
		return $aDataVariable;
	}
	
	// Returns the predictive data:
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
					foreach($aDataRegisterPrediction as $register) {
						$aValues[]=array("value"=>$register->getValue(), 
										 "date"=>$register->getDate()->format("Y-m-d H:i:s"));
					}
					
					$aDataVariablePrediction[]=array("name"=>$variable->getName(), 
													 "values"=>$aValues, 
													 "color"=>$variable->getColor(),	
													 "idSensor"=>$variable->getId(),
													 "units"=>$variable->getUnits());
				}				
			}
			$aDataPrediction[]=array("day"=>$aDays[$i], "variables"=>$aDataVariablePrediction);
		}		
		return $aDataPrediction;
		
	}
	
	// Process all historical data:
	private function getArrayFinalHistoricalVariables($aDataHistoric)
	{		
		$aDataFinal=array();
		
		for($h=0; $h<count($aDataHistoric); $h++)
		{				
			$nameVariable=$aDataHistoric[$h]['name'];
			$color=$aDataHistoric[$h]['color'];
			
			$aValues=array();
			$maxValue=$minValue=0;
			
			if(isset($aDataHistoric[$h]['values']))
			{
				$i=0;
				foreach($aDataHistoric[$h]['values'] as $value)
				{				/*
					$date=str_replace("T", " ", $value['datetime']);
					$dateFinal=substr($date, 0, -1);	*/
					$aValues[]=array("value"=>$value['value'], "date"=>$value['datetime']);
					
					if($maxValue<$value['value']) 			$maxValue=$value['value'];					
					if ($i==0)								$minValue= $value['value'];
					elseif ($minValue > $value['value'])	$minValue=$value['value'];
					
					$i++;
				}
			}
			$aDataFinal[]=array("name"=>$nameVariable, 
								"values"=>$aValues, 
								"type"=>'historical', 
								"units"=>$aDataHistoric[$h]['units'],	
								"idSensor"=>$aDataHistoric[$h]['idSensor'], 
								"max"=>$maxValue, 
								"min"=>$minValue,  
								"color"=>$color);
		}
			
		return $aDataFinal;
	}

	// Predictive data processing:
	private function getArrayFinalPredictionVariables($aDataPrediction)
	{
		$aDataFinal=array();
		$aNamesVariables=array();
		$aVariables=array();
		$aUnits=array();
		$aIdSensors=array();
		$aMaxs=array();
		$aMins=array();
		
		for($i=0; $i<count($aDataPrediction); $i++)
		{				
			//Para cada variable getValues de la predicción
			if(!empty($aDataPrediction[$i]['variables']))
			{				
				//Para cada variable obtner los valores del dia
				foreach($aDataPrediction[$i]['variables'] as $variable)
				{	
					$maxValue= 0;
					$minValue=0;
					
					if(!in_array($variable['name'], $aNamesVariables))
					{
						$aNamesVariables[]=$variable['name'];
						$aVariables[$variable['name']]=array();
						$aUnits[$variable['name']]=$variable['units'];
						$aIdSensors[$variable['name']]=$variable['idSensor'];
					}					
					$aValues=array();
					
					$j=0;
					foreach($variable['values'] as $register) {
						$aVariables[$variable['name']][]=array("value"=>$register['value'], "date"=>$register['date']);
						
						if($maxValue<$register['value']) 			$maxValue=$register['value'];					
						if ($j==0)								$minValue= $register['value'];
						elseif ($minValue > $register['value'])	$minValue=$register['value'];						
						$j++;
					}
					$aMaxs[$variable['name']]=$maxValue;
					$aMins[$variable['name']]=$minValue;
				}				
			}		
		}
		
		foreach($aVariables as $name=>$values) {
			$aDataFinal[]=array("name"=>$name, "values"=>$values, "type"=>'prediction', "color"=>"#ff0000", "units"=>$aUnits[$name], "idSensor"=>$aIdSensors[$name], "max"=>$maxValue, "min"=>$minValue);
		}
		
		return $aDataFinal;
	}

	// DEPRECATED: Process historical data for different days.
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
					if(strtotime($value['date'])>=strtotime($aDays[$i]) and strtotime($value['date'])<=strtotime($aDays[$i]." +1 day")) {
						$aValues[]=$value;
					}
				}
				
				$aVariable[]=array("name"=>$nameVariable, "values"=>$aValues);
			}
			$aDataFinal[]=array("day"=>$aDays[$i], "variables"=>$aVariable);
		}
			
		return $aDataFinal;
	}

	//Last date calculation of an action plan
	public function getLastCalculated($idActionPlan)
	{
		$lastCalculations=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findBy(array("fk_actionplan"=>$idActionPlan), 
             array('dateCreation' => 'DESC'));
		
		if($lastCalculations!=null)
		{
			$lastCalculation=$lastCalculations[0]->getDateCreation()->format("Y-m-d H:i:s");
		}else{
			$lastCalculation="no data";
		}
		
		return $lastCalculation;
	}
	
	//Last date forecast date of a building
	public function lastForecastedDate($idBuilding)
	{
		
		$lastDates=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findBy(
			array("fk_Building"=>$idBuilding),
            array('dateUser' => 'DESC'));
		
		if($lastDates!=null)
		{		
			$lastDate=$lastDates[0]->getDateUser()->format("Y-m-d H:i:s");			
		}else{
			$lastDate="no data";
		}
		
		return $lastDate;
	}

	//Last activitie user in 1 action plan
	public function getLastActionUser($idActionPlan, $user)
	{
		$lastEventsUsers=$this->em->getRepository('OptimusOptimusBundle:Events')->findBy(array("id_context"=>$idActionPlan, "fk_user"=>$user), 
             array('date' => 'DESC'));
		
		if($lastEventsUsers!=null)
		{			
			$lastAction=$lastEventsUsers[0]->getDate();
		}else{
			$lastAction=null;
		}
		
		return $lastAction;
	}
	
	//Get name building actual
	public function getNameBuilding($idBuilding)
	{
		$building=$this->em->getRepository('OptimusOptimusBundle:Building')->find($idBuilding);
		
		if($building!=null)
		{			
			$nameBuilding=$building->getName();
		}else{
			$nameBuilding='no data';
		}
		
		return $nameBuilding;
	}

	//Function 1.1: Get data chart for each Action Plan
	public function getDataChartTable($idBuilding, $idActionPlan, $startDateFunction, $colorsAP)
	{
		$allPartitions=$this->em->getRepository('OptimusOptimusBundle:BuildingPartitioning')->findBy(array("fk_Building"=>$idBuilding));
		
		//get Sensors for each partition
		$aPartitions=array();
		foreach($allPartitions as $partition)
		{
			$allAPSensors=$this->em->getRepository('OptimusOptimusBundle:APSensors')->findBy(array("fk_BuildingPartitioning"=>$partition->getId(), "fk_actionplan"=>$idActionPlan));
			
			$aDataSensors=array();
			foreach($allAPSensors as $apsensor)
			{
				$aDataSensors[$apsensor->getId()]=array("values"=>$this->getValuesChartSensor($idBuilding, $apsensor, $startDateFunction), "color"=>$colorsAP[$apsensor->getName()]);
			}
			$aPartitions[$partition->getId()]=$aDataSensors;
		}
		
		return $aPartitions;
	}
	
	//Function 1.2: Get values chart
	private function getValuesChartSensor($idBuilding, $apsensor, $startDateFunction)
	{
		$aDataSensor=array();
		$currentDate=\DateTime::createFromFormat('Y-m-d H:i:s', $startDateFunction);	
		
        // Get the current day -> the "first" one and the "last" one for calculating:
        $loInitDay=$currentDate->modify("+0 day")->format("Y-m-d H:i:s");
		$loFinalDay=$currentDate->modify("+6 day")->format("Y-m-d H:i:s");
		
        $aDays=$this->getDaysFromDate($loInitDay, $loFinalDay);	// Returns an array of every day between two dates	
		$numDays=count($aDays);									// Number of days: should be 7 (days)
		
		//Historical Data Sensor
		$aDataHistoric=$this->ontologia->getDataParameterFromOntology($loInitDay, $loFinalDay, $apsensor->getFkSensor()->getUrl(), $apsensor->getFkSensor()->getAggregation());
			
		$aHistorical=array(	"name"=>$apsensor->getName(), 
								"values"=>$aDataHistoric, 
								"color"=>$apsensor->getFkSensor()->getColor(),	
								"idSensor"=>$apsensor->getFkSensor()->getId(),	
								"units"=>$apsensor->getFkSensor()->getUnits());
		
		//Predictional Data Sensor
		$aValues=array();
		for($i=0; $i < $numDays; $i++)
		{
			$qPrediction=$this->em->getRepository('OptimusOptimusBundle:Prediction')->findPredictionByDate($aDays[$i], $idBuilding);
			//Para cada variable getValues de la predicción
			if(!empty($qPrediction))
			{
				$idPrediction=$qPrediction[0]->getId();				
												
				//get registros
				$aDataRegisterPrediction = $this->em->getRepository('OptimusOptimusBundle:RegisterPredictions')->findResgisterPredictionsByDate($apsensor->getFkSensor()->getId(), $idPrediction, $aDays[$i]);
				
				
				foreach($aDataRegisterPrediction as $register) {
					$aValues[]=array("value"=>$register->getValue(), 
									 "datetime"=>$register->getDate()->format("Y-m-d H:i:s")); //
				}							
			}
			//$aPrediction[]=array("day"=>$aDays[$i], "values"=>$aValues);
		}
		
		$aPrediction=array("name"=>$apsensor->getName(), 
								"values"=>$aValues, 
								"color"=>$apsensor->getFkSensor()->getColor(),	
								"idSensor"=>$apsensor->getFkSensor()->getId(),
								"units"=>$apsensor->getFkSensor()->getUnits());
		
		return $aDataSensor=array("historical"=>$aHistorical, "prediction"=>$aPrediction);
	}
}

?>