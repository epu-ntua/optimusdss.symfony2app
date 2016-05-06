<?php

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;

use Optimus\OptimusBundle\Entity\Sensor;

class SemanticFrameworkController extends Controller
{
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// This method queries virtuoso server to generate a specific data set for the creation of a predictive model. 
	// The data retrived will start from ($date -$windows hours) to ($date).
	//  - predictedparameter: name of the parameter to be predicted
	//  - date: date of the last date to be predicted. If it is not provided the default value is today!
	//  - window: number of elements (hours) of data needed to be predicted. By the default 7 days: 168 hours
	//  - sensors: This parameteres are the IDs of the sensors needed separated by &. the list of sensors is ordered   
	//			   and each "predictedparameter" should know its own list of sensors needed
	//
	// It returns a specific CSV document with $window rows for the prediction model. It is used by RapidMiner processes
	//
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public function get_data_for_modelAction($predictedparameter='irradiation', $date='', $window=168, $sensors="1&2&3&4&5&6&7&8&9")
	{
		////////////////////////////////////////////////////////////		
		////////////////////////////////////////////////////////////
		// It is a special case because we do NOT have weather data
		if($predictedparameter == 'indoortemperature') {
			
			// Reading a CSV file !Hardcoded!!
			$this->readCSVFile('santcugat_indoor_temperatureClean.csv');
			
			return new Response("");
		}
		
		if($predictedparameter == 'production') {
			
			// Reading a CSV file !Hardcoded!!
			$this->readCSVFile('clean_weather_and_pv_dataSantCugat.csv');
			
			return new Response("");
		}
		
		if($predictedparameter == 'consumption') {
			
			// Reading a CSV file !Hardcoded!!
			$this->readCSVFile('creating_model_energy_consumption.csv');
			
			return new Response("");
		}
		//
		////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////
		
		
		
		if($date == '')
			return new Response("");
		
		////////////////////////////////////////////////////////////////////////
		// 1. Processing the input date and window paramters

		$date = str_replace("T", " ", $date);
		$date = str_replace("Z", "", $date);
		
		$to=\DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$from=\DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$from = $from->add(date_interval_create_from_date_string('-'.$window.' hours'));
		
		//echo "FROM: ".$from->format('Y-m-d H:i:s')."<br>";
		//echo " TO : ".$to->format('Y-m-d H:i:s')."<br><br>-----------------------<br>";
		//
		////////////////////////////////////////////////////////////////////////
		// 2. Call the method to get data from Virtuoso

		$array_ret = $this->get('ServiceOntologia')->getDataFromSensorList($from, $to, $window, $sensors);

		//
		////////////////////////////////////////////////////////////////////////
		// 3. Printing the result
		// 3.1 Printing the list of parameters needed to predict. This list should be the same as the list of sensors in $sensors parameter.
		
		$header = "";
		if($predictedparameter == 'production') $header = 'TemperatureC;Humidity;PressurehPa;WindDirectionDegrees;SolarRadiationWatts.m.2;DewpointC;WindSpeedKMH;production;radiation;datetime\n';
		else if($predictedparameter == 'indoortemperature') $header = 'TemperatureC;Humidity;PressurehPa;WindDirectionDegrees;SolarRadiationWatts.m.2;DewpointC;WindSpeedKMH;production;radiation;datetime\n';
		else if($predictedparameter == 'consumption') $header = 'TemperatureC;Humidity;PressurehPa;WindDirectionDegrees;SolarRadiationWatts.m.2;DewpointC;WindSpeedKMH;production;radiation;Consumption;datetime\n';

		echo $header;

		////////////////////////
		// 3.2 Printing the array of data
		for($i = 0; $i < count($array_ret); $i++) {
			$row = $array_ret[$i][0];
			for($j = 1; $j < count($array_ret[$i])-1; $j++) {
				$row .= ";".$array_ret[$i][$j];
			}
		
			echo $row.";".$array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s')."\n";
		}
		//
		////////////////////////
		
		return new Response("");
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// This method queries virtuoso server to generate a specific data set for the creation of a predictive model 
	//  - predictedparameter: name of the parameter to be predicted
	//  - date: date of the last date to be predicted. If it is not provided the default value is today!
	//  - horizon: number of elements (hours) to be predicted. By the default 7 days: 168 hours
	//
	// It returns a specific CSV document with 48 + $horizon rows for the prediction. It is used by RapidMiner processes
	//
	public function get_data_for_forecastingAction($predictedparameter='irradiation', $date='', $window=168, $sensors="")
	{		
		////////////////////////////////////////////////////////////
		////////////////////////////////////////////////////////////
		// It is a special case because we do NOT have weather data
		//if($predictedparameter == 'indoortemperature') {
			// Reading a CSV file !Hardcoded!!
		//	$this->readCSVFile('santcugat_indoor_temperatureClean.csv');
			
		//	return new Response("");
		//}
		//
		////////////////////////////////////////////////////////////		
		////////////////////////////////////////////////////////////
		
		
		
		if($date == '')
			return new Response("");
		
		////////////////////////////////////////////////////////////////////////
		// 1. Processing the input date and window paramters

		$date = str_replace("T", " ", $date);
		$date = str_replace("Z", "", $date);
		
		$from=\DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$to=\DateTime::createFromFormat('Y-m-d H:i:s', $date);
		$to = $to->add(date_interval_create_from_date_string($window.' hours'));

		//
		////////////////////////////////////////////////////////////////////////
		// 2. Call the method to get data from Virtuoso

		$array_ret = $this->get('ServiceOntologia')->getDataFromSensorList($from, $to, $window, $sensors);

		//
		////////////////////////////////////////////////////////////////////////
		// 3. Printing the result
		// 3.1 Printing the list of parameters needed to predict. This list should be the same as the list of sensors in $sensors parameter.
		
		$header = "";
		if($predictedparameter == 'production') $header = 'Temp;RelHumidity;Windspeed;WindDirection;pressure;Globalradiation;Cloudcover;Hourlyaccumulatedrain;Hourlyaccumulatedsnow;Weathercode;Confidencelevel;datetime\n';
		else if($predictedparameter == 'production_alarm') $header = 'Temp;RelHumidity;Windspeed;WindDirection;pressure;Globalradiation;Cloudcover;Hourlyaccumulatedrain;Hourlyaccumulatedsnow;Weathercode;Confidencelevel;production;datetime\n';
		else if($predictedparameter == 'indoortemperature') $header = 'indoortemp;TemperatureC;datetime\n';
		else if($predictedparameter == 'consumption') $header = 'TemperatureC;Humidity;PressurehPa;WindDirectionDegrees;SolarRadiationWatts.m.2;DewpointC;WindSpeedKMH;radiation;datetime\n';

		echo $header;
		////////////////////////
		// 3.2 Printing the array of data
		for($i = 0; $i < count($array_ret); $i++) {
			$row = $array_ret[$i][0];
			for($j = 1; $j < count($array_ret[$i])-1; $j++) {
				$row .= ";".$array_ret[$i][$j];
			}
		
            $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
            $date = str_replace(" ", "T", $date)."Z";
			echo $row.";".$date."\n";
		}
		//
		////////////////////////
		
		
		return new Response("");
	}
	
	/////////////////////////////////////////////
	/////////////////////////////////////////////
	/////////////////////////////////////////////
	// It is a special Method because we do NOT have weather data

	private function readCSVFile($filename) 
	{
		$direction=__DIR__."/temp/".$filename;
		
		$gestor = @fopen($direction, "r");
		
		if ($gestor) {
		  while (($buffer = fgets($gestor)) !== false) {
			  echo $buffer;
		  }
		  if (!feof($gestor)) {
			echo "Error reading file\n";
		  }
		  fclose($gestor);
	
		}
	}
	/////////////////////////////////////////////
	/////////////////////////////////////////////
	/////////////////////////////////////////////
	
}