<?php

namespace Optimus\OptimusBundle\Servicios;

use Symfony\Component\Config\FileLocator;
use Doctrine\ORM\EntityManager;

use Optimus\OptimusBundle\Entity\APCalculation;
//use Optimus\OptimusBundle\Entity\APPVOutput;
//use Optimus\OptimusBundle\Entity\APPVOutputDay;
use Optimus\OptimusBundle\Entity\Prediction;
use Optimus\OptimusBundle\Entity\RegisterPredictions;

class ServiceAPAdaptativeCalculation {
 
    protected $em;
	protected $ontologia;
	protected $invokePredictData; 
	protected $events;
	
	// These names describe a unique relation between a sensor and this action plan.
    // These names can be the same to describe the same relation between a sensor and other action plans
	
 	private static $sensor_energyProduction_name = "Energy Production";	
	private static $sensor_energyConsumption_name = "Energy Consumption"; 
	private static $sensor_energyPricePurchase_name = "Energy Price Buy"; 
	private static $sensor_energyPriceSelling_name = "Energy Price Sell";
	
	public function getEnergyProductionName(){return self::$sensor_energyProduction_name;}
	public function getEnergyConsumptionName(){return self::$sensor_energyConsumption_name;}
	public function getEnergyPricePurchaseName(){return self::$sensor_energyPricePurchase_name;}
	public function getEnergyPriceSellingName(){return self::$sensor_energyPriceSelling_name;}
 
    public function __construct(EntityManager $em,
								ServiceOntologia $ontologia,
								ServicePredictDataInvoke $invokePredictData,
								ServiceEvents $events)
    {       
		$this->em=$em;
		$this->ontologia=$ontologia;
		$this->invokePredictData=$invokePredictData;
		$this->events=$events;
    }
	
	public function insertNewCalculation($aoRelSensorsActionPlan, $from, $calculation, $idBuilding, $ip, $user)
    {
	
	}
	
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[].=self::$sensor_energyProduction_name;
		$aVariablesInput[].=self::$sensor_energyConsumption_name;
		$aVariablesInput[].=self::$sensor_energyPricePurchase_name;
		$aVariablesInput[].=self::$sensor_energyPriceSelling_name;
		
		return $aVariablesInput;
	}
}
?>

