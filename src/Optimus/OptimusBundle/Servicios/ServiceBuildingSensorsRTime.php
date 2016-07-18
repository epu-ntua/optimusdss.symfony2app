<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class ServiceBuildingSensorsRTime
{
    protected $em;
	protected $datacapturing;
	protected $ontologia;
	
	
	protected $CO2emissionsCoefficient = 0.399; //kgCO2/kWh from: http://www.minetur.gob.es/energia/desarrollo/EficienciaEnergetica/RITE/propuestas/Documents/2014_03_03_Factores_de_emision_CO2_y_Factores_de_paso_Efinal_Eprimaria_V.pdf
	
	// These names describe a unique relation between a sensor and this action plan.
    // These names can be the same to describe the same relation between a sensor and other action plans
	
 	private static $sensor_energyProduction_name = "Produced renewable energy";	
	private static $sensor_energyConsumption_name = "Energy consumption"; 
	private static $sensor_co2_name = "CO2"; 
	private static $sensor_energyCost_name = "Energy cost";
	private static $sensor_indoorTemperature_name = "Indoor temperature";
	private static $sensor_outdoorTemperature_name = "Outdoor temperature";
	
	private static $sensor_energyProduction_color = "#01d98e";	
	private static $sensor_energyConsumption_color = "#8900e9"; 
	private static $sensor_co2_color = "#9f9d9e"; 
	private static $sensor_energyCost_color = "#f39a02";
		
	private static $sensor_energyProduction_units = "kWh";	
	private static $sensor_energyConsumption_units = "kWh"; 
	private static $sensor_co2_units = "kgCO2"; 
	private static $sensor_energyCost_units = "€";
	
	
	public function getEnergyProductionName(){return self::$sensor_energyProduction_name;}
	public function getEnergyConsumptionName(){return self::$sensor_energyConsumption_name;}
	public function getCO2Name(){return self::$sensor_co2_name;}
	public function getEnergyCostName(){return self::$sensor_energyCost_name;}
	public function getIndoorTemperatureName(){return self::$sensor_indoorTemperature_name;}
	public function getOutdoorTemperatureName(){return self::$sensor_outdoorTemperature_name;}
 
    public function __construct(EntityManager $em, ServiceDataCapturing $datacapturing, ServiceOntologia $ontologia)
    {       
		$this->em=$em;
		$this->datacapturing = $datacapturing;
		$this->ontologia = $ontologia;
    }
		
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[].=self::$sensor_energyProduction_name;
		$aVariablesInput[].=self::$sensor_energyConsumption_name;
		$aVariablesInput[].=self::$sensor_co2_name;
		$aVariablesInput[].=self::$sensor_energyCost_name;
		$aVariablesInput[].=self::$sensor_indoorTemperature_name;
		$aVariablesInput[].=self::$sensor_outdoorTemperature_name;
		
		return $aVariablesInput;
	}
	
	public function getUnitsVariables()
	{
		$data = array();
		$data[self::$sensor_energyCost_name] = self::$sensor_energyCost_units;
		$data[self::$sensor_co2_name] = self::$sensor_co2_units;
		$data[self::$sensor_energyConsumption_name] = self::$sensor_energyConsumption_units;
		$data[self::$sensor_energyProduction_name] = self::$sensor_energyProduction_units;
		
		return $data;
	}

	public function getColorsVariables()
	{
		$data = array();
		$data[self::$sensor_energyCost_name] = self::$sensor_energyCost_color;
		$data[self::$sensor_co2_name] = self::$sensor_co2_color;
		$data[self::$sensor_energyConsumption_name] = self::$sensor_energyConsumption_color;
		$data[self::$sensor_energyProduction_name] = self::$sensor_energyProduction_color;
		
		return $data;
	}
	
	public function getVariablesGraphStack()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[]=array("name"=>self::$sensor_energyProduction_name, "color"=> self::$sensor_energyProduction_color);
		$aVariablesInput[]=array("name"=>self::$sensor_energyConsumption_name, "color"=> self::$sensor_energyConsumption_color);
		$aVariablesInput[]=array("name"=>self::$sensor_co2_name, "color"=> self::$sensor_co2_color);
		$aVariablesInput[]=array("name"=>self::$sensor_energyCost_name, "color"=> self::$sensor_energyCost_color);	
		
		return $aVariablesInput;
	}
	
		
	public function getDataforRenderingChart($to, $from, $idBuilding)
	{
		$aMapping=array();
		
		$dataProduction = array();
		$dataConsumption = array();
		$dataCo2 = array();
		$dataCost = array();
		
		
		$maxValue = 1;
		$minValue = 0;		
		$maxValueProd = 1;
		$minValueProd = 0;		
		$maxValueCost = 1;
		$minValueCost = 0;		
		$maxValueCo2 = 1;
		$minValueCo2 = 0;	
		
		
		////////////////////////////
		// 1. Energy consumption
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_energyConsumption_name));

		
		if($sensorMapping) {
			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataConsumption  = $this->datacapturing->getDataVariables($sensor, $to, $from, '', 'variable', $idBuilding);
		}
		
		////////////////////////////
		// 2. CO2 emissions
		//$dataCo2 = $dataConsumption;
        $sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_co2_name));
		
		if($sensorMapping) {
			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataCo2  = $this->datacapturing->getDataVariables($sensor, $to, $from, '', 'variable', $idBuilding);
		}
			
		////////////////////////////
		// 3. Energy cost
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_energyCost_name));

		if($sensorMapping) {
			
			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataCost  = $this->datacapturing->getDataVariables($sensor, $to, $from, '', 'variable', $idBuilding);
		}			
		
		////////////////////////////
		// 4. Energy production
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_energyProduction_name));

				
		if($sensorMapping) {

			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataProduction  = $this->datacapturing->getDataVariables($sensor, $to, $from, '', 'variable', $idBuilding);
		}
		if(count($dataConsumption) > 0) {
			for($i = 0; $i < count($dataConsumption[0]["values"]); $i++ )
			{
				if($maxValue < $dataConsumption[0]["values"][$i]["value"]) 	$maxValue = $dataConsumption[0]["values"][$i]["value"];
				if($minValue > $dataConsumption[0]["values"][$i]["value"]) 	$minValue = $dataConsumption[0]["values"][$i]["value"];
				
				//Data production correction and Max/Min
				if(count($dataProduction) > 0) {
					if($maxValueProd < $dataProduction[0]["values"][$i]["value"]) 	$maxValueProd = $dataProduction[0]["values"][$i]["value"];
					if($minValueProd > $dataProduction[0]["values"][$i]["value"]) 	$minValueProd = $dataProduction[0]["values"][$i]["value"];
				}

				if($maxValueCo2 < $dataCo2[0]["values"][$i]["value"]) 	$maxValueCo2 = $dataCo2[0]["values"][$i]["value"];
				if($minValueCo2 > $dataCo2[0]["values"][$i]["value"]) 	$minValueCo2 = $dataCo2[0]["values"][$i]["value"];
				
		
				if($maxValueCost < $dataCost[0]["values"][$i]["value"]) 	$maxValueCost = $dataCost[0]["values"][$i]["value"];
				if($minValueCost > $dataCost[0]["values"][$i]["value"]) 	$minValueCost = $dataCost[0]["values"][$i]["value"];
			}
		
			if($maxValueProd > $maxValue)
				$maxValue = $maxValueProd;
			
			if($maxValue > $maxValueProd)
				$maxValueProd = $maxValue;
			
			
			$aMapping[]=array("name"=>self::$sensor_energyConsumption_name, "color"=>self::$sensor_energyConsumption_color, "stack" => 0, "units"=>self::$sensor_energyConsumption_units,  "maxValue"=>$maxValue, "minValue"=>$minValue, "data"=>$dataConsumption[0]["values"]);
			
            $aMapping[]=array("name"=>self::$sensor_co2_name, "color"=>self::$sensor_co2_color, "stack" => 0, "units"=>self::$sensor_co2_units, "maxValue"=>$maxValueCo2, "minValue"=>$minValueCo2, "data"=>$dataCo2[0]["values"]);
					
			
			if(count($dataCost) > 0) 
				$aMapping[]=array("name"=>self::$sensor_energyCost_name, "color"=>self::$sensor_energyCost_color, "stack" => 0, "units"=>self::$sensor_energyCost_units, "maxValue"=>$maxValueCost, "minValue"=>$minValueCost, "data"=>$dataCost[0]["values"]);
			else
				$aMapping[]=array("name"=>self::$sensor_energyCost_name, "color"=>self::$sensor_energyCost_color, "stack" => 0, "units"=>self::$sensor_energyCost_units, "maxValue"=>$maxValueCost, "minValue"=>$minValueCost, "data"=>array());

			if(count($dataProduction) > 0) 
				$aMapping[]=array("name"=>self::$sensor_energyProduction_name, "color"=>self::$sensor_energyProduction_color, "stack" => 1, "units"=>self::$sensor_energyProduction_units , "maxValue"=>$maxValueProd, "minValue"=>$minValueProd, "data"=>$dataProduction[0]["values"]);
			else
				$aMapping[]=array("name"=>self::$sensor_energyProduction_name, "color"=>self::$sensor_energyProduction_color, "stack" => 1, "units"=>self::$sensor_energyProduction_units , "maxValue"=>$maxValueProd, "minValue"=>$minValueProd, "data"=>array());					
		}

		return $aMapping;
	}
	
	
	
	public function getRTTime($to, $from, $dateActual='', $idBuilding)
	{
		$data = array();
		$data[self::$sensor_energyCost_name] = 0;
		$data[self::$sensor_co2_name] = 0;
		$data[self::$sensor_energyConsumption_name] = 0;
		$data[self::$sensor_energyProduction_name] = 0;
		
		$dataProduction = array();
		$dataConsumption = array();
		$dataCo2 = array();
		$dataCost = array();
		
		
		$maxValue = 1;
		$minValue = 0;		
		$maxValueProd = 1;
		$minValueProd = 0;		
		$maxValueCost = 1;
		$minValueCost = 0;		
		$maxValueCo2 = 1;
		$minValueCo2 = 0;	
		
		
		////////////////////////////
		// 1. Energy consumption
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_energyConsumption_name));

		
		if($sensorMapping) {
			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataConsumption  = $this->datacapturing->getDataVariables($sensor, $to, $from, $dateActual, 'variable', $idBuilding);			
		}
		
		////////////////////////////
		// 2. CO2 emissions
		//$dataCo2 = $dataConsumption;
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_co2_name));

		if($sensorMapping) {
			
			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataCo2  = $this->datacapturing->getDataVariables($sensor, $to, $from, $dateActual, 'variable', $idBuilding);
		}		
			
		////////////////////////////
		// 3. Energy cost
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_energyCost_name));

		if($sensorMapping) {
			
			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataCost  = $this->datacapturing->getDataVariables($sensor, $to, $from, $dateActual, 'variable', $idBuilding);
		}			
		
		////////////////////////////
		// 4. Energy production
		$sensorMapping=$this->em->getRepository('OptimusOptimusBundle:BuildingSensorsRTime')->findBy(array("fk_Building" => $idBuilding, "name"=>self::$sensor_energyProduction_name));

				
		if($sensorMapping) {

			$sensor = $this->em->getRepository('OptimusOptimusBundle:Sensor')->findById($sensorMapping[0]->getFkSensor());	
			
			$dataProduction  = $this->datacapturing->getDataVariables($sensor, $to, $from, $dateActual, 'variable', $idBuilding);
		}
			
			
		if(count($dataConsumption) > 0) {
			for($i = 0; $i < count($dataConsumption[0]["values"]); $i++ )
			{
				$data[self::$sensor_energyConsumption_name] += $dataConsumption[0]["values"][$i]["value"];
				
				if(count($dataProduction) > 0) {
					$data[self::$sensor_energyProduction_name] += $dataProduction[0]["values"][$i]["value"] /*/1000*/;
				}	

				if(count($dataCost) > 0) {
                    $data[self::$sensor_energyCost_name] += $dataCost[0]["values"][$i]["value"] /*/1000*/;
					
				}
				if(count($dataCo2) > 0) {
                    $data[self::$sensor_co2_name] += $dataCo2[0]["values"][$i]["value"] /*/1000*/;
					
				}
			}	
		}
				
		return $data;
	}
}
?>