<?php

namespace Optimus\OptimusBundle\Servicios;

use Symfony\Component\Config\FileLocator;
use Doctrine\ORM\EntityManager;



class ServiceAPEnergySource {

    protected $em;
    
	// Sensors
	//protected $ontologia;
	//protected $invokePredictData; 
	protected $events;
	private static $sensor_PV_rad = "PV_rad";	
	private static $sensor_PV_Pel = "PV_Pel"; 
	private static $sensor_CHP_C65B_Pel = "CHP_C65B_Pel"; 
	private static $sensor_CHP_C65_Pel = "CHP_C65_Pel";
	private static $sensor_STO_Pel = "STO_Pe";
	private static $sensor_NET_Pel = "NET_Pel";		
	private static $sensor_BO_Pth = "BO_Pth";	
	
	public function getPV_radName(){return self::$sensor_PV_rad;}
	public function getPV_PelName(){return self::$sensor_PV_Pel;}
	public function getCHP_C65B_PelName(){return self::$sensor_CHP_C65B_Pel;}
	public function getCHP_C65_PelName(){return self::$sensor_CHP_C65_Pel;}
	public function getCHP_STO_PelName(){return self::$sensor_STO_Pel;}
	public function getNET_PelName(){return self::$sensor_NET_Pel;}
	public function getCHP_BO_PthName(){return self::$sensor_BO_Pth;}
	
	public function getDataVariablesInput()
	{
		$aVariablesInput=array();
		
		$aVariablesInput[].=self::$sensor_PV_rad;
		$aVariablesInput[].=self::$sensor_PV_Pel;
		$aVariablesInput[].=self::$sensor_CHP_C65B_Pel;
		$aVariablesInput[].=self::$sensor_CHP_C65_Pel;
		$aVariablesInput[].=self::$sensor_STO_Pel;
		$aVariablesInput[].=self::$sensor_NET_Pel;
		$aVariablesInput[].=self::$sensor_BO_Pth;
		
		return $aVariablesInput;
	}
	
	//Sensors\
	
	
    public function __construct(EntityManager $em)
    {
        $this->em=$em;
    }
	
    /*
     * Run the service
     * The result is an array containing
     *   - Input from sensors (e.g 'actual_humidity', 'air_temperature')
     *   - Feedback from TCV ('feedback')
     *   - The proposed temperature ('proposed_temperature')
     */
    public function invoke_service($building_id) {
        $result = array();
        /* Get initial data about the building */
        $building = $this->em->getRepository('OptimusOptimusBundle:Building')->find($building_id);
        $result['building'] = $building;
		
        $week = array();
        $dataDaily = array();
		
		/* Open the input file: 'Real.csv', now 'LoadShift.csv' */
        $configDirectories = array(__DIR__.'\Util\EnergySource');
        $locator = new FileLocator($configDirectories);
        $path = $locator->locate('LoadShift_combined.csv', null, false)[0];
		
		$file_shaving = fopen($path,"r");
		
        fgetcsv($file_shaving);
		$dataHourly = array();
		$batteryUse = array();
        if(! feof($file_shaving)){
			$row = fgetcsv($file_shaving);
			$date = substr($row['1'], 0, 10);
			$hour = substr($row['1'], 11, 2);
			for($i=0; $i<$hour; $i++){
				array_push($dataHourly, array("consumption"=> 'undefined', "production"=> 'undefined', "grid"=> 'undefined', "finalSuggestion"=> 'undefined', "consumptionShifted"=> 'undefined', "gridShifted"=> 'undefined', "load"=>'undefined', "gridOriginal"=> 'undefined', "batteryUse"=>'undefined'));
				array_push($batteryUse, 'undefined');
			}
			array_push($dataHourly, array("consumption"=> $row['0'], "production"=> $row['4'], "grid"=> $row['0'], "finalSuggestion"=> $row['3'], "consumptionShifted"=> $row['2'] + $row['3'], "gridShifted"=> $row['0'] + $row['3'], "load"=> $row['2'], "gridOriginal"=> $row['6'], "batteryUse"=> $row['7']));
			array_push($batteryUse, $row['7']);
		}
		$start_hour = $hour;
		for($i=0; $i<(24*7 - $start_hour); $i++){
			if(! feof($file_shaving)){
				$row = fgetcsv($file_shaving);
				$date = substr($row['1'], 0, 10);
				$hour = substr($row['1'], 11, 2);
				array_push($dataHourly, array("consumption"=> $row['0'], "production"=> $row['4'], "grid"=> $row['0'], "finalSuggestion"=> $row['3'], "consumptionShifted"=> $row['2'] + $row['3'], "gridShifted"=> $row['0'] + $row['3'],"load"=> $row['2'], "gridOriginal"=> $row['6'], "batteryUse"=> $row['7']));
				array_push($batteryUse, $row['7']);
			}
			else{
				array_push($dataHourly, array("consumption"=> 'undefined', "production"=> 'undefined', "grid"=> 'undefined', "finalSuggestion"=> 'undefined', "consumptionShifted"=> 'undefined', "gridShifted"=> 'undefined', "load"=> 'undefined', "gridOriginal"=> 'undefined', "batteryUse"=>'undefined'));
				array_push($batteryUse, 'undefined');
			}
		}
		
		fclose($file_shaving);
		
      
		
		
		
		//for($i=0; $i<24*7; $i++){
			//array_push($batteryUse, $row['7']);
			//array_push($batteryUse, "no data");
		//}
		
		
		for($j=0; $j<7; $j++){
			$dayData = array("consumption"=> 0, "production"=> 0, "grid"=> 0, "load"=> 0);
			for($i=0; $i<24; $i++){
				if($dataHourly[$j*24+$i]["consumption"] != "undefined"){
					$dayData["consumption"] += $dataHourly[$j*24+$i]["consumption"];
					$dayData["production"] += $dataHourly[$j*24+$i]["production"];
					$dayData["grid"] += $dataHourly[$j*24+$i]["grid"];
					$dayData["load"] += $dataHourly[$j*24+$i]["load"];
				}
			}
			array_push($dataDaily, $dayData);
		}
		
        //array_splice($dataHourly, count($dataHourly)-1, 1);
        
		$result['week'] = $week;
		$result['dataDaily'] = $dataDaily;
		$result['dataHourly'] = $dataHourly;
		$result['batteryUse'] = $batteryUse;
		
		
		
        return $result;
    }
}
?>
