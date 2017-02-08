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
			->addArgument(
                'city',
                InputArgument::OPTIONAL,
                'For which city you want to invoke the virtual sensors?'
            )
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$city = $input->getArgument('city');
		$output->writeln("------** Virtual Status for ".$city." **--------");
		$output->writeln("");
				
		if(strcmp(strtolower($city), "savona") == 0 ) {
			$this->savonaSchoolPVsensor($output);
			$this->savonaSchoolEnergyConsumptionsensor($output);
			$this->savonaSchoolCO2sensor($output);
			$this->savonaSchoolEnergyPricesensor($output);
			
			
			$this->savonaCampusPVsensor($output);
			$this->savonaCampusEnergyConsumptionsensor($output);
			$this->savonaCampusCO2sensor($output);
			$this->savonaCampusEnergyPrice($output);
			
		} else if(strcmp(strtolower($city), "zaanstad") == 0 ) {
			$this->zaanstadEnergyConsumptionsensor($output);
		} else if(strcmp(strtolower($city), "santcugat") == 0 ) {
			$this->santcugatCO2Pricesensors($output);
			$this->santcugatPVsensor($output);
			
			$this->santcugatTheatreVirtualsensors($output);
		} else if(strcmp(strtolower($city), "epu") == 0 ) {
            $this->epuEnergyConsumptionsensor($output);
			$this->epuCO2sensor($output);
			$this->epuEnergyCostsensor($output);
        }
		else if(strcmp(strtolower($city), "rae") == 0 ) {
            $this->raeEnergyConsumptionsensor($output);
        }

		$output->writeln("");
		$output->writeln("------** END **--------");
	}

    /*
    /********************************************************************
    * EPU
    *
    */
    protected function epuEnergyConsumptionsensor($output)
    {
        $output->writeln(" - epuEnergyConsumptionsensor -");

        //the first one is the virtual sensor (47)
        //id47 = id11 + d12 + d13 + d14
        $arr_sensors = "47_11_12_13_14";

        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $vconsumptionSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("47");
        $virturl = $vconsumptionSensor->getUrl();

        $array_ret = $this->getValues($arr_sensors);

        //For each hour
        for($i = 0; $i < count($array_ret); $i++) {
            $value = 0;
            $allWithValue = 1;
            //For each sensor, after the first and before the last(date)
            for($j = 1; $j < count($array_ret[$i])-1; $j++) {
                if($array_ret[$i][$j] > -1) {
                    $value += $array_ret[$i][$j];
                }
                else{
                    $allWithValue = 0;
                    break;
                }
            }
            if($allWithValue == 1) {
                $date = $array_ret[$i][count($array_ret[$i]) - 1]->format('Y-m-d H:i:s');
                $date = str_replace(" ", "T", $date) . "Z";

                $this->insertData($virturl, "epu", "http://optimus_epu", "total_energy_consumption_47", $value, $date);
            }
        }
    }
	
	protected function epuCO2sensor($output)
    {
        $output->writeln(" - epuCO2sensor -");

		$emission_factor = 1.1236;
        //the first one is the virtual sensor (47)
        //id48 = (id11 + d12 + d13 + d14) * emission_factor
        $arr_sensors = "48_11_12_13_14";

        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $vconsumptionSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("48");
        $virturl = $vconsumptionSensor->getUrl();

        $array_ret = $this->getValues($arr_sensors);

        //For each hour
        for($i = 0; $i < count($array_ret); $i++) {
            $value = 0;
            $allWithValue = 1;
            //For each sensor, after the first and before the last(date)
            for($j = 1; $j < count($array_ret[$i])-1; $j++) {
                if($array_ret[$i][$j] > -1) {
                    $value += $array_ret[$i][$j] * $emission_factor;
                }
                else{
                    $allWithValue = 0;
                    break;
                }
            }
            if($allWithValue == 1) {
                $date = $array_ret[$i][count($array_ret[$i]) - 1]->format('Y-m-d H:i:s');
                $date = str_replace(" ", "T", $date) . "Z";

                $this->insertData($virturl, "epu", "http://optimus_epu", "co2_emissions_48", $value, $date);
            }
        }
    }
	
	protected function epuEnergyCostsensor($output)
    {
        $output->writeln(" - epuEnergyCostsensor -");

		$price = 0.03886;
        //the first one is the virtual sensor (47)
        //id48 = (id11 + d12 + d13 + d14) * emission_factor
        $arr_sensors = "49_11_12_13_14";

        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $vconsumptionSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("49");
        $virturl = $vconsumptionSensor->getUrl();

        $array_ret = $this->getValues($arr_sensors);

        //For each hour
        for($i = 0; $i < count($array_ret); $i++) {
            $value = 0;
            $allWithValue = 1;
            //For each sensor, after the first and before the last(date)
            for($j = 1; $j < count($array_ret[$i])-1; $j++) {
                if($array_ret[$i][$j] > -1) {
                    $value += $array_ret[$i][$j] * $price;
                }
                else{
                    $allWithValue = 0;
                    break;
                }
            }
            if($allWithValue == 1) {
                $date = $array_ret[$i][count($array_ret[$i]) - 1]->format('Y-m-d H:i:s');
                $date = str_replace(" ", "T", $date) . "Z";

                $this->insertData($virturl, "epu", "http://optimus_epu", "energy_cost_49", $value, $date);
            }
        }
    }
	
	
	/*
    /********************************************************************
    * RAE
    *
    */
    protected function raeEnergyConsumptionsensor($output)
    {
        $output->writeln(" - raeEnergyConsumptionsensor -");

        //the first one is the virtual sensor (42)
        //id42 = id20
        $arr_sensors = "42_20";

        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $vconsumptionSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("42");
        $virturl = $vconsumptionSensor->getUrl();

        $array_ret = $this->getValues($arr_sensors);

        //For each hour
        for($i = 0; $i < count($array_ret); $i++) {
            $value = 0;
            $allWithValue = 1;
            //For each sensor, after the first and before the last(date)
            for($j = 1; $j < count($array_ret[$i])-1; $j++) {
                if($array_ret[$i][$j] > -1) {
                    $value += $array_ret[$i][$j];
                }
                else{
                    $allWithValue = 0;
                    break;
                }
            }
            if($allWithValue == 1) {
                $date = $array_ret[$i][count($array_ret[$i]) - 1]->format('Y-m-d H:i:s');
                $date = str_replace(" ", "T", $date) . "Z";

                $this->insertData($virturl, "rae", "http://optimus_rae", "total_energy_consumption_42", $value, $date);
            }
        }
    }
	
	
	/*
	/********************************************************************
	* SANT CUGAT
	*
	*/
	
	protected function santcugatCO2Pricesensors($output) 
	{
		$output->writeln(" - Santcugat CO2 & Price sensors -");
		
		// http://www.optimus-smartcity.eu/resource/sant_cugat/sensingdevice/townhall_co2emissions 1147
		// http://www.optimus-smartcity.eu/resource/sant_cugat/sensingdevice/townhall_energyprice 1148
		//the first one is the virual sensor
		$arr_sensors = "4_1146_58";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1147");
		$virturlCO2 = $nameSensor->getUrl();
		
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1148");
		$virturlPrice = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
	
			$value = $array_ret[$i][0];
            $prod = $array_ret[$i][1];
			$price = $array_ret[$i][2];
			
			$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
			$date = str_replace(" ", "T", $date)."Z";
		 
			$this->insertData($virturlCO2, "sant_cugat", "http://optimus_santcugat", "townhall_co2emissions", $value * 0.399, $date);
			$this->insertData($virturlPrice, "sant_cugat", "http://optimus_santcugat", "townhall_energyprice", ($value-$prod)*$price/1000, $date);
		}
	}

	protected function santcugatPVsensor($output) 
	{
		$output->writeln(" - santcugatPVsensor -");
		
		//the first one is the virual sensor
		$arr_sensors = "1146_1";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1146");
        
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;

			//only if the virtyal sensor do not have data
			//if($array_ret[$i][0] == -1) {
                $novalue = 1;
				for($j = 1; $j < count($array_ret[$i])-1; $j++) {
					if($array_ret[$i][$j] >= 0) {
						$value += $array_ret[$i][$j];
                        $novalue = 0;
					}
				}
					
                if( $novalue == 0) {                    
                    $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
                    $date = str_replace(" ", "T", $date)."Z";
				 
                    $this->insertData($virturl, "sant_cugat", "http://optimus_santcugat", "energy_production", $value/1000, $date);
                }
			//}
		}
	}
	
	
	protected function santcugatTheatreVirtualsensors($output)
    {
        $output->writeln(" - santcugatTheatreEnergyConsumptionsensors -");

        //the first one is the virtual sensor (1152)
        //id1152 = id1149
        $arr_sensors = "1149_58";

        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $vSensor1 = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1152");
        $virturl1 = $vSensor1->getUrl();
		$vSensor2 = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1154");
        $virturl2 = $vSensor2->getUrl();
		$vSensor3 = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1155");
        $virturl3 = $vSensor3->getUrl();

        $array_ret = $this->getValues($arr_sensors);

        //For each hour
        for($i = 0; $i < count($array_ret); $i++) {
            $consumption = 0;
            $allWithValue = 1;
            //For each sensor, after the first and before the last(date)
            for($j = 1; $j < count($array_ret[$i])-1; $j++) {
                if($array_ret[$i][$j] < 0) {
                    $allWithValue = 0;
                    break;
                }
            }
            if($allWithValue == 1) {
				
                $consumption = $array_ret[$i][0];
				$emissions = $consumption * 0.399;
				$cost = $consumption * $array_ret[$i][1] /1000;
                
				
                $date = $array_ret[$i][count($array_ret[$i]) - 1]->format('Y-m-d H:i:s');
                $date = str_replace(" ", "T", $date) . "Z";

                $this->insertData($virturl1, "sant_cugat", "http://optimus_santcugat", "theatre_energy_consumption_vs", $consumption, $date);
				$this->insertData($virturl2, "sant_cugat", "http://optimus_santcugat", "theatre_co2_emissions_vs", $emissions, $date);
				$this->insertData($virturl3, "sant_cugat", "http://optimus_santcugat", "theatre_energy_cost_vs", $cost, $date);
            }
						
        }
    }

		
	/*
	/*
	/********************************************************************
	* SAVONA ZAANSTAD
	*
	*/
	protected function zaanstadEnergyConsumptionsensor($output)
	{
		$output->writeln(" - zaanstadEnergyConsumptionsensor -");
		
		//the first one is the virual sensor
        //id25 + id26 + id27) + (id60 + id61 + id62) - (id65 + id66 + id67),
		$arr_sensors = "322_11_12_13";
		
		$entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("322");
		$virturl = $nameSensor->getUrl();
		
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("323");
		$costsensor = $nameSensor->getUrl();
			
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("324");
		$co2sensor = $nameSensor->getUrl();
		
		$array_ret = $this->getValues($arr_sensors);
		$prevalue = 0;
		for($i = 1; $i < count($array_ret); $i++) {
			$value = 0;
           
            
			//only if the virtyal sensor do not have data
			//if($array_ret[$i][0] == -1) {
                $novalue = 1;
				for($j = 1; $j < count($array_ret[$i])-1; $j++) {
					if($array_ret[$i][$j] > -1) {
						$val = ($array_ret[$i][$j]-$array_ret[$i-1][$j]);
						
						if($val >= 0 && $val < 1000) 
							$value += $val;
						else  {
							$value = $prevalue;
							break;
						}
                        					
                        $novalue = 0;
                    }
				}				
				
                if( $novalue == 0) {
					
                    $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
                    $date = str_replace(" ", "T", $date)."Z";
				
					var_dump($value." at ".$date);
                    $this->insertData($virturl, "zaanstad", "http://optimus_zaanstad", "energy_consumption", $value, $date);
					$this->insertData($costsensor, "zaanstad", "http://optimus_zaanstad", "energycost", 56.0, $date);//56€/MWh
					$this->insertData($co2sensor, "zaanstad", "http://optimus_zaanstad", "co2emissions", $value*0.399, $date);//56€/MWh
                
					$prevalue = $value;
				}
			//}
		}
	}
	
	
	/*
	/********************************************************************
	* SAVONA CAMPUS
	*
	*/
	protected function savonaCampusPVsensor($output)
	{
		
		//http://www.optimus-smartcity.eu/resource/savona/sensingdevice/campus_energy_production
		$output->writeln(" - Savona Campus PVsensor -");
		
		//the first one is the virual sensor
		$arr_sensors = "1044_1021_1001_1003";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1044");
        
		$virturl = $nameSensor->getUrl();
		
		$this->deleteData($virturl, "savona", "http://optimus_savona", "campus_energy_production", "", "");
			
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;

			$value = $array_ret[$i][1];								//1021
			if($array_ret[$i][2] < 0) $value -= $array_ret[$i][2];	//1001
			if($array_ret[$i][3] < 0) $value -= $array_ret[$i][3];	//1003
			         
			$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
			$date = str_replace(" ", "T", $date)."Z";
			 
			//dump($date." Vale: ".$value." | ".$array_ret[$i][1].", ".$array_ret[$i][2].", ".$array_ret[$i][3]);
	 
			$this->insertData($virturl, "savona", "http://optimus_savona", "campus_energy_production", $value, $date);
		}
	}
	
	
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*/
	protected function savonaCampusEnergyConsumptionsensor($output)
	{
		//http://www.optimus-smartcity.eu/resource/savona/sensingdevice/campus_energy_consumption
		$output->writeln(" - Savona Campus EnergyConsumption -");
		
		//the first one is the virual sensor
		$arr_sensors = "1045_1005_1006_1044";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1045");
        
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;

			for($j = 1; $j < count($array_ret[$i])-1; $j++) {
				$value += $array_ret[$i][$j];
			}
				
			$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
			$date = str_replace(" ", "T", $date)."Z";
			 
			$this->insertData($virturl, "savona", "http://optimus_savona", "campus_energy_consumption", $value, $date);
		}
	}
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*/
	protected function savonaCampusCO2sensor($output)
	{
		//http://www.optimus-smartcity.eu/resource/savona/sensingdevice/campus_co2_emissions
		$output->writeln(" - Savona Campus CO2 -");
		
		//the first one is the virual sensor
		$arr_sensors = "1046_1005_1006";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1046");
        
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;

			for($j = 1; $j < count($array_ret[$i])-1; $j++) {
				$value += $array_ret[$i][$j];
			}
			
			$value *= 0.483;
			
			if($value < 0) $value = 0;
			
			$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
			$date = str_replace(" ", "T", $date)."Z";
			 
			$this->insertData($virturl, "savona", "http://optimus_savona", "campus_co2_emissions", $value, $date);
		}
	}
		
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*/
	protected function savonaCampusEnergyPrice($output)
	{
		//http://www.optimus-smartcity.eu/resource/savona/sensingdevice/campus_energy_price
		$output->writeln(" - Savona Campus Energy Price -");
		
		//the first one is the virual sensor
		$arr_sensors = "1047_1005_1006_1042";
		
		$entityManager = $this->getContainer()->get('doctrine')->getManager();
        
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1047");
        
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;

			$value += $array_ret[$i][1];			//1005
			$value += $array_ret[$i][2];			//1006
			
			$value *=  ($array_ret[$i][3] / 1000);	//1042
			
			if($value < 0) $value = 0;
			
			$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
			$date = str_replace(" ", "T", $date)."Z";
			 
			$this->insertData($virturl, "savona", "http://optimus_savona", "campus_energy_price", $value, $date);
		}
	}
	
	/*
	/********************************************************************
	* SAVONA SCHOOOL 
	*
	*/
	protected function savonaSchoolPVsensor($output)
	{
		$output->writeln(" - Savona School PVsensor -");
		
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
					if($array_ret[$i][$j] >= 0) {
						$value += $array_ret[$i][$j];
                        $novalue = 0;
					}
				}
					
                if( $novalue == 0) {                    
                    $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
                    $date = str_replace(" ", "T", $date)."Z";
				 
				 //fdsfsd
                    $this->insertData($virturl, "savona", "http://optimus_savona", "energy_production", $value/1000, $date);
					//$this->deleteData($virturl, "savona", "http://optimus_savona", "energy_consumption", $value, $date);
                }
			//}
		}
	}
	
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*
	*/
	protected function savonaSchoolEnergyConsumptionsensor($output)
	{
		$output->writeln(" - Savona School EnergyConsumptionsensor -");
		
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
                $novalue = 0;
				for($j = 1; $j < count($array_ret[$i])-1; $j++) {
					if($j < 7)  $value += $array_ret[$i][$j];
                    else if($array_ret[$i][$j] >= 0) {
						$value += $array_ret[$i][$j];
					
//                        $novalue = 0;
                    }
				}				
				
                if( $novalue == 0) {
                    $date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
                    $date = str_replace(" ", "T", $date)."Z";
				
                    $this->insertData($virturl, "savona", "http://optimus_savona", "energy_consumption", $value/1000, $date);
					
					//$this->deleteData($virturl, "savona", "http://optimus_savona", "energy_consumption", $value, $date);
                }
			//}
		}
	}
			
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*
	*/
	protected function savonaSchoolCO2sensor($output)
	{
		$output->writeln(" - Savona School CO2 emissions -");
		
		//the first one is the virual sensor
        //id25 + id26 + id27) + (id60 + id61 + id62) - (id65 + id66 + id67),
		$arr_sensors = "1048_25_26_27_60_61_62";
		
		$entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1048");
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;
           
			$novalue = 0;
			for($j = 1; $j < count($array_ret[$i])-1; $j++) {
				$value += $array_ret[$i][$j];
			}				
			
			$value *= 0.483/1000;
			
			if($value < 0) $value = 0;				
			
			if( $novalue == 0) {
				$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
				$date = str_replace(" ", "T", $date)."Z";
			
				$this->insertData($virturl, "savona", "http://optimus_savona", "school_co2_emissions", $value, $date);
				
			}
		}
	}
	
	
	/*
	* This virtual sensor is the sum of id65 + id66 + id67
	*
	*/
	protected function savonaSchoolEnergyPricesensor($output)
	{
		$output->writeln(" - Savona School Energy Price emissions -");
		
		//the first one is the virual sensor
        
		$arr_sensors = "1049_25_26_27_60_61_62_1042";
		
		$entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
		$nameSensor = $entityManager->getRepository('OptimusOptimusBundle:Sensor')->findOneById("1049");
		$virturl = $nameSensor->getUrl();
		
				
		$array_ret = $this->getValues($arr_sensors);
		
		for($i = 0; $i < count($array_ret); $i++) {
			$value = 0;
           
			$novalue = 0;
			for($j = 1; $j < count($array_ret[$i])-2; $j++) {
				$value += $array_ret[$i][$j];
			}				
			
			$value /= 1000;
			$value *= ($array_ret[$i][count($array_ret[$i])-2] / 1000);	//1042
			
			if($value < 0) $value = 0;				
			
			if( $novalue == 0) {
				$date = $array_ret[$i][count($array_ret[$i])-1]->format('Y-m-d H:i:s');
				$date = str_replace(" ", "T", $date)."Z";
			
				$this->insertData($virturl, "savona", "http://optimus_savona", "school_energy_price", $value, $date);
				
			}
		}
	}
	
	
	
	/********************************************************************
	* GENERAL METHODS
	*
	*
	*/
	
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
		
		//var_dump($insert);
        $this->getContainer()->get('service_ontologia')->insertData($insert);
	}
	
	protected function deleteData($virturl, $pilot, $graph, $name, $value, $datetime)
	{		
		$insert = 
		'DELETE DATA FROM <'.$graph.'> {
			?so <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?val.
		
		} WHERE {
			?obs <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <'.$virturl.'> .
			?obs <http://purl.oclc.org/NET/ssnx/ssn#observationResult> ?so.
			?so <http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?val.
		}'; 
		
		var_dump($insert);
       // $this->getContainer()->get('service_ontologia')->insertData($insert);
	}
	
	protected function getValues($arr_sensors)
	{
		$window = 368;
		
		$date = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d"), date("Y")));  
		$to=\DateTime::createFromFormat('Y-m-d H:i:s', $date." 00:00:00");
		$from=\DateTime::createFromFormat('Y-m-d H:i:s', $date." 00:00:00");
        $from = $from->add(date_interval_create_from_date_string('-'.$window.' hours'));

		var_dump($date);
		var_dump($to);
		var_dump($from);

		
		$data = $this->getContainer()->get('service_ontologia')->lastNRecordsListOfSensors($from, $to, $window, $arr_sensors); // <--- !!!!!!! TEMP
		
		//var_dump($data);
		
		return $data;
	}
	
}