<?php

namespace Optimus\OptimusBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class VirtualSensorsCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('VirtualSensors')
            ->setDescription('Calculation of the virtual sensors')            
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$output->writeln("------** Sensor Status**--------");
		$output->writeln("");
		
		
		$this->savonaPVsensor($output);
		$this->savonaEnergyConsumptionsensor($output);
		
		

		$output->writeln("");
		$output->writeln("------** END **--------");
	
	}
	
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*
	*/
	protected function savonaPVsensor($output)
	{
		//the first one is the virual sensor
		$arr_sensors = "1040_65_66_67";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1040");
        
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;

			//only if the virtyal sensor do not have data
			//if($array_ret[$i][0] == -1) {
                $novalue = 1;
				for($j = 1; $j < count($array_ret[$i])-1; $j++) {
					if($array_ret[$i][$j] > -1) {
						$value += $array_ret[$i][$j];
                        $novalue = 0;
					}
				}
					
                if( $novalue == 0) {                    
                    $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
                    $date = str_replace(" ", "T", $date)."Z";
				
                    $this->insertData($virturl, "savona", "http://optimus_savona", "energy_production", $value, $date);
                }
			//}
		}
	}
	
		/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*
	*/
	protected function savonaEnergyConsumptionsensor($output)
	{
		//the first one is the virual sensor
        //id25 + id26 + id27) + (id60 + id61 + id62) - (id65 + id66 + id67),
		$arr_sensors = "1041_25_26_27_60_61_62_65_66_67";
		
		$entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1041");
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;
           
            
			//only if the virtyal sensor do not have data
			//if($array_ret[$i][0] == -1) {
                $novalue = 1;
				for($j = 1; $j < count($array_ret[$i])-1; $j++) {
					if($array_ret[$i][$j] > -1) {
						if($j < 7)  $value += $array_ret[$i][$j];
                        else        $value -= $array_ret[$i][$j];
					
                        $novalue = 0;
                    }
				}				
				
                if( $novalue == 0) {
                    $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
                    $date = str_replace(" ", "T", $date)."Z";
				
                    $this->insertData($virturl, "savona", "http://optimus_savona", "energy_consumption", $value, $date);
                }
			//}
		}
	}
	
	protected function insertData($virturl, $pilot, $graph, $name, $value, $datetime)
	{		
		$id = str_replace("T","", $datetime);
		$id = str_replace("Z","", $id);
		$id = str_replace(":","", $id);
		$id = str_replace("-","", $id);
	
		
		$insert = 
		'INSERT INTO <'.$graph.'> {
            <http://www.optimus-smartcity.eu/resource/'.$pilot.'/featureofinterest/'.$name.'> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://optimus-smartcity.eu/ontology-releases/eu/semanco/ontology/optimus.owl#'.$name.'Feature> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/observation/'.$name.''.$id.'> <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <'.$virturl.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/observation/'.$name.''.$id.'> <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> <http://www.optimus-smartcity.eu/resource/'.$pilot.'/instant/'.$id.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/featureofinterest/'.$name.'> <http://purl.oclc.org/NET/ssnx/ssn#hasProperty> <http://www.optimus-smartcity.eu/resource/'.$pilot.'/property/'.$name.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/sensoroutput/'.$name.''.$id.'> <http://purl.oclc.org/NET/ssnx/ssn#hasValue> "'.$value.'"^^<http://www.w3.org/2001/XMLSchema#decimal> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/observation/'.$name.''.$id.'> <http://purl.oclc.org/NET/ssnx/ssn#observedProperty> <http://www.optimus-smartcity.eu/resource/'.$pilot.'/property/'.$name.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/observation/'.$name.''.$id.'> <http://purl.oclc.org/NET/ssnx/ssn#observationResult> <http://www.optimus-smartcity.eu/resource/'.$pilot.'/sensoroutput/'.$name.''.$id.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/observation/'.$name.''.$id.'> <http://purl.oclc.org/NET/ssnx/ssn#featureOfInterest> <http://www.optimus-smartcity.eu/resource/'.$pilot.'/featureofinterest/'.$name.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/sensoroutput/'.$name.''.$id.'> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://optimus-smartcity.eu/ontology-releases/eu/semanco/ontology/optimus.owl#'.$name.'SensorOutput> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/property/'.$name.'> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://semanco-tools.eu/ontology-releases/eu/semanco/ontology/ontology/optimus.owl#'.$name.'> .
			<http://www.optimus-smartcity.eu/resource/'.$pilot.'/instant/'.$id.'> <http://www.w3.org/2006/time#inXSDDateTime> "'.$datetime.'"^^<http://www.w3.org/2001/XMLSchema#dateTime> .
			<'.$virturl.'> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://optimus-smartcity.eu/ontology-releases/eu/semanco/ontology/optimus.owl#'.$name.'VirtualSensor> .

		}'; 
		
		var_dump($insert);
        $this->getContainer()->get('service_ontologia')->insertData($insert);
	}
	
	
	protected function getValues($arr_sensors)
	{
		$window = 168;
		
		$date = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d"), date("Y")));  
		$to=\DateTime::createFromFormat('Y-m-d H:i:s', $date." 00:00:00");
		$from=\DateTime::createFromFormat('Y-m-d H:i:s', $date." 00:00:00");
		
		var_dump($date);
		var_dump($to);
		var_dump($from);
		$from = $from->add(date_interval_create_from_date_string('-'.$window.' hours'));
		
		$data = $this->getContainer()->get('service_ontologia')->lastNRecordsListOfSensors($from, $to, $window, $arr_sensors); // <--- !!!!!!! TEMP
		
		//var_dump($data);
		
		return $data;
	}
	
}