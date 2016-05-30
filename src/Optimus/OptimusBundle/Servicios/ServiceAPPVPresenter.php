<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Optimus\OptimusBundle\Servicios\GestorOntologia;

class ServiceAPPVPresenter
{
	// Variables members --------------------------------------------------------------------------
				
	// Static members:
	private static $green = "green";
	private static $finance = "finance";
	private static $peak = "peak";
	
	private static $miNone = 0;
	private static $miSellSurplus = 1;
	private static $miShiftElecLoad = 2;
	private static $miCreateSurplus = 3;
	private static $miCoverSurplus = 3;
	private static $miCoverSurplusShiftElecLoad = 4;
	
	private static $sensor_energyProduction_name = "Energy Production";		
	private static $sensor_solarRadiation_name = "Solar Radiation";			
	
	protected $em;
	protected $ontologia;
	protected $maDataIntervalDay;
	protected $energy_demand_threshold_percentage;
	protected $daily_penalty_energy_threshold;
	
	// Constructors -------------------------------------------------------------------------------

	public function __construct(EntityManager $em,
					ServiceOntologia $ontologia, 
					$energy_demand_threshold_percentage, 
					$daily_penalty_energy_threshold)
    {
        // Params: htdocs\optimus\app\config\config.yml
        //         htdocs\optimus\src\Optimus\OptimusBundle\Resources\config\services.yml
	
        $this->ontologia=$ontologia;
		$this->em=$em;

		$this->$energy_demand_threshold_percentage = $energy_demand_threshold_percentage;
		$this->$daily_penalty_energy_threshold = $daily_penalty_energy_threshold;
    }
	
    // Methods ------------------------------------------------------------------------------------
	
    public function getDataValues($aoDaySelectedOnTheCalendar, $idActionPlan)
    {
        $loCurrentDate=\DateTime::createFromFormat('Y-m-d H:i:s', $aoDaySelectedOnTheCalendar);	
		
        // Get the current day -> the "first" one and the "last" one for calculating:
        $loInitDay=$loCurrentDate->modify("+0 day")->format("Y-m-d H:i:s");
		$loFinalDay=$loCurrentDate->modify("+6 day")->format("Y-m-d H:i:s");
		
        $loLstDays=$this->getDaysFromDate($loInitDay, $loFinalDay);	// Returns an array of every day between two dates	
		$liNumDays=count($loLstDays);								// Number of days: should be 7 (days)	
		$aDataActionPlan=array();
		$maxIntervals=1;

		// Total values:
		$this->weekSoldProducedAcumulated = 0;
		$this->weekEmissionsAcumulated = 0;
			
		// for each day from starting date to 6 more days ...
		for($i=0; $i < $liNumDays; $i++)
		{
			// Variables:
			$aDataCalculation=array();
			
			$day_production=0;
			$day_consumption=0;
			$day_difference=0;

			$day_production_sold=0;
			$day_consumption_saved=0;
			$day_co2_emissions=0;

			$purchase_price=0;
			$selling_price=0;			

			$idStatusDay=0;			
			$idCalculation=0;		
			$idOutputDay=0;
			$status=0;
			$strategy="no";	
			$options = array();
			$options[0] = "-";												

			//$differentValue=false;

			// Get the last calculation for the current day:
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($loLstDays[$i], $idActionPlan);
			$dayLookFor=\DateTime::createFromFormat("Y-m-d H:i:s", $loLstDays[$i]);
			$lsAbbreviatedDay=\DateTime::createFromFormat("Y-m-d H:i:s", $loLstDays[$i]);
			$lsAbbreviatedDayFinal = $lsAbbreviatedDay;
			$lsAbbreviatedDayFinal = $lsAbbreviatedDayFinal->format('d-m');
			$nameAbbreviatedDay = $lsAbbreviatedDay->format('D');
			$dayFinal=explode(" ", $loLstDays[$i])[0];
												
            //dump("Calculation");
			//dump($qCalculation);
			
			// if there is "calculation data" for this day...
			if(!empty($qCalculation)) 
			{
				try 
				{
					//dump("Calculation");
					$idCalculation=$qCalculation[0]->getId();

					//dump($idCalculation);
					//dump($dayFinal);

					// Get the value of strategy per day (id, fk_ap_calculation, date, status, strategy)(query -> SELECT appvoutputday...)
					// Inputs -> Action Calculation "ID" (e.g. 1)
					//        -> Current day in the loop (e.g. 2015-06-15)
					// Return -> (e.g. 1, 24, 2015-06-05, 1, Green)
					$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $dayFinal); //
					if($outputDay)
					{
						$idOutputDay=$outputDay[0]->getId();		// 1, 2....
						$status=$outputDay[0]->getStatus();			// 0=Unknown, 1=Accepted, 2=Declined 
						$strategy=$outputDay[0]->getStrategy();		// Green, Finance, Peak (intermediate)
						$strategy = strtolower($strategy);
						
						// TO BE REMOVED!!!!!!!!!!!!!!!
						if($strategy == "intermediate")
						   $strategy = "peak";
					}
				} 
				catch (Exception $e) 
				{
					//echo 'Exception: ',  $e->getMessage(), "\n";
					continue;
				}			
				
				// 1. Get PV information: "Prices" and "Production"
				$before=false;	
				if(isset($qCalculation[1]))
				{
					$idCalculationBefore=$qCalculation[1]->getId();
					$aRegisterCalculationBefore = $this->em->getRepository('OptimusOptimusBundle:APPVOutput')->findResgisterOutputsByDate($idCalculationBefore, $loLstDays[$i]);
					
					if($aRegisterCalculationBefore)
					{
						$before=true;
						$dateCalculationBefore=$qCalculation[1]->getDateCreation()->format('Y-m-d H:i:s');
					}
				}

				$idCalculation=$qCalculation[0]->getId();
				$aRegisterCalculation = $this->em->getRepository('OptimusOptimusBundle:APPVOutput')->findResgisterOutputsByDate($idCalculation, $loLstDays[$i]);
				// Example: energyPrice: 1.5 <-----------------------------------
				// 			      energyProduction: 698.0 <----------------------------  

				// 2. For each register -> (HOUR)
				$numRegisters=0;
				$hourlyCumulativeIncomePV=0;
				$this->maDataIntervalDay=array();
				
				$suggestion=-1; 												
																
				if (strcmp($strategy, self::$green) == 0) 
				{
					$options[0] = "-"; 													// $miNone
					$options[1] = "Sell energy surplus";										// $miSellSurplus
					$options[2] = "Try to shift electric load";    // $miShiftElecLoad
				}
				else if (strcmp($strategy, self::$finance) == 0) 
				{
					$options[0] = "-";		// $miNone
					$options[1] = "Sell energy surplus";           // $miSellSurplus
					$options[2] = "Try to shift electric load";    // $miShiftElecLoad 
					$options[3] = "Try to create a surplus";							// $miCreateSurplus
				}														
				else if (strcmp($strategy, self::$peak) == 0) 
				{
					$options[0] = "-";// $miNone
					$options[1] = "Sell energy surplus";           // $miSellSurplus
					$options[2] = "Shift electric load here";      // $miShiftElecLoad 
					$options[3] = "Cover the energy surplus";						// $miCoverSurplus
					$options[4] = "Cover the energy surplus/Shift electric load here"; // $miCoverSurplusShiftElecLoad
				}														
													
			// ---------------------------------------------------------------------------------
			// LOAD DEMO VALUES (extracted from Excel test) ------------------------------------
			// ---------------------------------------------------------------------------------
			
			// GREEN TEST
			// D, C, K, E (Energy purchase price [€/kWh], 
			//             Energy sell price [€/kWh], 
			//             Energy Demand [kWh],
			//             Energy generation from PV [kWh]
			/*
				$aRegisterCalculation2=array( array(0.03020,0.05,50.078,0), // 0
									array(0.02762,0.05,51.679,0), // 1
									array(0.02546,0.05,61.066,0), // 2
		array(0.02549,0.05,81.649,0), // 3
		array(0.02478,0.05,135.16,0), // 4
		array(0.02543,0.05,159.786,0), // 5
		array(0.02900,0.05,206.349,0), // 6
		array(0.03293,0.05,223.045,0), // 7
		array(0.04126,0.05,241.261,2.886404055), // 8
		array(0.04417,0.05,243.347,23.01299261), // 9
		array(0.04495,0.05,252,75.87412397), // 10
		array(0.04417,0.05,284.82,126.6300608), // 11
																																		array(0.04540,0.05,239,188.5884912), // 13
																																		array(0.04417,0.05,227.405,200.3850726), // 14
																																		array(0.04417,0.05,190.521,201.5574482), // 15
																																		array(0.04417,0.04,168.957,189.5027366), // 16
																																		array(0.04460,0.04,154.415,166.3215259), // 17
																																		array(0.04600,0.04,125.212,145.7273393), // 18
																																		array(0.04417,0.05,49.496,73.667642), // 19
																																		array(0.04417,0.05,50.526,30.47022708), // 20
																																		array(0.04658,0.05,49.17,5.385637364), // 21
																																		array(0.04719,0.05,51.152,0.459896307), // 22
																																		array(0.04417,0.05,48.919,0)  // 23
																																		);
				*/																										
/*
// FINANCE TEST																
	$aRegisterCalculation2=array( array(0.03020,0.05,50.078,0), // 0
array(0.02762,0.05,51.679,0), // 1
array(0.02546,0.05,61.066,0), // 2
array(0.02549,0.05,81.649,0), // 3
array(0.02478,0.05,135.16,0), // 4
array(0.02543,0.05,159.786,0), // 5
array(0.02900,0.05,206.349,0), // 6
array(0.03293,0.05,223.045,0), // 7
array(0.04126,0.05,241.261,2.886404055), // 8
array(0.04417,0.05,243.347,23.01299261), // 9
array(0.04495,0.05,252,75.87412397), // 10
array(0.04417,0.05,284.82,126.6300608), // 11
array(0.04495,0.05,252.602,163.7916834), // 12
array(0.04540,0.05,239,188.5884912), // 13
array(0.04417,0.05,227.405,220.3850726), // 14
array(0.04417,0.05,190.521,201.5574482), // 15
array(0.04417,0.04,168.957,189.5027366), // 16
array(0.04460,0.04,154.415,166.3215259), // 17
array(0.04600,0.04,125.212,145.7273393), // 18
array(0.04417,0.05,49.496,73.667642), // 19
array(0.04417,0.05,50.526,30.47022708), // 20
array(0.04658,0.05,49.17,5.385637364), // 21
array(0.04719,0.05,51.152,0.459896307), // 22
array(0.04417,0.05,48.919,0)  // 23
);
*/
					/*
				 // PEAK TEST:
					$aRegisterCalculation2=array( array(0.03020,0.05,50.078,0), // 0
array(0.02762,0.05,51.679,0), // 1
array(0.02546,0.05,61.066,0), // 2
array(0.02549,0.05,81.649,0), // 3
array(0.02478,0.05,135.16,0), // 4
array(0.03293,0.05,159.786,0), // 5
array(0.03293,0.05,206.349,0), // 6
array(0.03293,0.05,223.045,0), // 7
array(0.04126,0.05,241.261,2.886404055), // 8
array(0.04417,0.04,243.347,23.01299261), // 9
array(0.04495,0.04,252,75.87412397), // 10
array(0.04417,0.05,284.82,126.6300608), // 11
array(0.04495,0.04,252.602,163.7916834), // 12
array(0.04540,0.04,239,188.5884912), // 13
array(0.04417,0.04,227.405,220.3850726), // 14
array(0.04417,0.05,190.521,201.5574482), // 15
array(0.02478,0.022,168.957,189.5027366), // 16
array(0.04417,0.04,154.415,166.3215259), // 17
array(0.02549,0.04,125.212,145.7273393), // 18
array(0.02478,0.04,49.496,73.667642), // 19
array(0.02543,0.04,50.526,30.47022708), // 20
array(0.04658,0.05,49.17,5.385637364), // 21
array(0.04719,0.05,51.152,0.459896307), // 22
array(0.04417,0.05,48.919,0)  // 23
																																			);																																													
				*/							
				//-----------------------------------------------------------------------------------
	
				// Pre-calculations for PEAK strategy:
				$EdemEgen = 0;
				$EdemEgenArray = array();
				$extraDemand = 0;
				$extraDemandSum = 0;
				$energySurplusSum = 0;
				$PercentilePrice = 0.20;
				$PercentilePeak = 0.85;
				$purchasePriceArray = array();																
				$Percentile = 0;
				$PercentileEdem = 0;
																
				if (strcmp($strategy, self::$peak) == 0) 
				{
					$iTemp1=0;	
					foreach($aRegisterCalculation as $register)
					{
						$demand=$register->getEnergyConsumption();				     
						$production=$register->getEnergyProduction();						
						// ---------------------------------------------------------------------------------
						// LOAD DEMO VALUES (extracted from Excel test) ------------------------------------
						// ---------------------------------------------------------------------------------																				
						// $register2=$aRegisterCalculation2[$iTemp1];
						// $demand=$register2[2];			
						// $production=$register2[3];																				
						// ---------------------------------------------------------------------------------
		
						if($demand - $production < 0) { $EdemEgen = 0; }
						else $EdemEgen = $demand - $production;
						$EdemEgenArray[$iTemp1] = $EdemEgen;
						
						$iTemp1++;																							
					}																				
					$PercentileEdem = $this->getPercentile($EdemEgenArray,$PercentilePeak);
					
					$iTemp1=0;	
					foreach($aRegisterCalculation as $register)
					{
						$purchase_price=$register->getEnergyPrice();		     
						$selling_price=$register->getEnergyPriceSelling();	
						$demand=$register->getEnergyConsumption();				     
						$production=$register->getEnergyProduction();						
						// ---------------------------------------------------------------------------------
						// LOAD DEMO VALUES (extracted from Excel test) ------------------------------------
						// ---------------------------------------------------------------------------------																				
						// $register2 =$aRegisterCalculation2[$iTemp1];																								
						// $purchase_price=$register2[0];
						// $selling_price=$register2[1];
						// $demand=$register2[2];			
						// $production=$register2[3];
						// ---------------------------------------------------------------------------------
																				
						if($demand - $production < 0) { $EdemEgen = 0; }
						else $EdemEgen = $demand - $production;
																								
						if($EdemEgen < $PercentileEdem) { $extraDemand = 0; }
						else { $extraDemand = $EdemEgen - $PercentileEdem; }
						$extraDemandSum = $extraDemandSum + $extraDemand;		// 0000																																																																				
																																																
						if ($production > $demand){	$EnergySurplus = $production - $demand; } 
						else	{ $EnergySurplus = 0;	}
						if ($selling_price > $purchase_price) { $priceCheck = true;	}
						else	{ $priceCheck = false; }	
						if(!$priceCheck) {	$energySurplusSum = $energySurplusSum + $EnergySurplus; }
						
						$purchasePriceArray[$iTemp1] = $purchase_price;
						
						$iTemp1++;
					}
																				
					$Percentile = $this->getPercentile($purchasePriceArray ,$PercentilePrice);
					
					// dump("--------------------");
					// dump($Percentile);
					// dump($energySurplusSum);
					// dump($extraDemandSum);
				}
													
																// for each hour... (0..23)
																$iii = 0;
																foreach($aRegisterCalculation as $register)
																{
																    //dump(">>> ".$register->getEnergyPrice());
																
																				// optimus\src\Optimus\OptimusBundle\Entity\...
																				$purchase_price=$register->getEnergyPrice();		     // (D: Energy purchase price [€/kWh])
																				$selling_price=$register->getEnergyPriceSelling();	// (C: Energy sell price [€/kWh])		
																				$demand=$register->getEnergyConsumption();				     // (K: Is the same for all hours)
																				$production=$register->getEnergyProduction();						// (E: Energy generation from PV [kWh])
																				
																				// ---------------------------------------------------------------------------------
																				// LOAD DEMO VALUES (extracted from Excel test) ------------------------------------
																				// ---------------------------------------------------------------------------------																				
																				// $register2 =$aRegisterCalculation2[$iii];
																				// $iii++;
																				// $purchase_price=$register2[0];
																				// $selling_price=$register2[1];
																				// $demand=$register2[2];			
																				// $production=$register2[3];																				
																				// ---------------------------------------------------------------------------------
																				
																				$purchase_price_before=0;
																				$selling_price_before=0;
																				$demand_before=0;
																				$production_before=0;
																				
																				$hour=$register->getHour();					
																				$hour = $hour->format('H:i');
																				
																				$message_hour = "-";
																				
																				// Comprobamos datos del cálculo anterior:
																				
																				if($before==true) 
																				{
																								foreach($aRegisterCalculationBefore as $registerBefore)
																								{
																												if($register->getHour()== $registerBefore->getHour()) 
																												{
																																$purchase_price_before=$registerBefore->getEnergyPrice();		     
																																$selling_price_before=$registerBefore->getEnergyPriceSelling();
																																$demand_before=$registerBefore->getEnergyConsumption();					
																																$production_before=$registerBefore->getEnergyProduction();
																												}
																								}
																				}																															
														
																				// RULES --------------------------------------------------------------------------
														
																				$color_suggestion = "999999";
																				$EnergySurplus = 0;
																				$priceCheck = false;
														
															     if ($production > $demand){
																									$EnergySurplus = $production - $demand; // Energy surplus [kWh] = Energy generation from PV [kWh] - Energy Demand [kWh]
																				} 
																				else	{ 
																								$EnergySurplus = 0;
																				}
																				
																				if ($selling_price > $purchase_price) { $priceCheck = true;	}
																				else	{ $priceCheck = false; }							
														
																				if (strcmp($strategy, self::$green) == 0) 
																				{			
																								// Strategy 1: GREEN ----------------------------------------------------------
																								
																								// suggestion values:
																								// 0: - (gray) $miNone
																								// 1: "Sell energy surplus" (white) $miSellSurplus
																								// 2: "Try to shift electric load" (green) $miAutoConsumption
																								
																								//dump(($iii-1).": ".$selling_price." > ".$purchase_price." (".$priceCheck.")");																						
																								
																								if($EnergySurplus > 0)
																								{ 
																												if ($priceCheck) 
																												{
																																// SellSurplus ($miSellSurplus = 0):																												  
																																$suggestion = $this->checkNewInterval($suggestion, $this::$miSellSurplus, $hour); 	
																																$color_suggestion = "FFFFFF";
																																$message_hour = $options[$this::$miSellSurplus]; // "Sell energy surplus";	
																												}																										
																												else 
																												{	
																																// AutoConsumption ($miAutoConsumption = 1):
																																$suggestion = $this->checkNewInterval($suggestion, $this::$miShiftElecLoad, $hour); 	
																																$color_suggestion = "AAEEAA";
																																$message_hour = $options[$this::$miShiftElecLoad]; // "Try to shift electric load"; 
																												}
																								}
																								else {
																										$color_suggestion = "999999";
																										$suggestion = -1;
																								}
																								
																								//dump($suggestion);
																				}
																				elseif (strcmp($strategy, self::$finance) == 0) 
																				{
																								// Strategy 2: FINANCE ----------------------------------------------------------------
																								
																								// suggestion values:
																								// 0: - (gray) $miNone
																								// 1: "Sell energy surplus" (white) $miSellSurplus
																								// 2: "Try to shift electric load" (green) $miAutoConsumption
																								// 3: "Try to create a surplus" (white) $miCreateSurplus
																								
																								$PotentialSurplusHours = false;
																								$generationWithoutSurplus = $production - $EnergySurplus;
																								$edemEgen = $demand - $generationWithoutSurplus;
																								if(($edemEgen < 0.1*$generationWithoutSurplus)&&
																											($EnergySurplus == 0) &&
																											($selling_price > $purchase_price))
																								{
																											$PotentialSurplusHours = true;
																								}																						
																								
																								if(($EnergySurplus > 0) && ($priceCheck))
																								{ 
																												// SellSurplus ($miSellSurplus = 0):																												  
																												$suggestion = $this->checkNewInterval($suggestion, $this::$miSellSurplus, $hour); 
																												$color_suggestion = "FFFFFF";
																												$message_hour = $options[$this::$miSellSurplus]; // "Sell energy surplus";	
																								}																										
																								else 
																								{	
																												if(($EnergySurplus > 0) && (!$priceCheck))
																												{
																																// AutoConsumption ($miAutoConsumption = 1):
																																$suggestion = $this->checkNewInterval($suggestion, $this::$miShiftElecLoad, $hour); 	
																																$color_suggestion = "AAEEAA";
																																$message_hour = $options[$this::$miShiftElecLoad]; // "Try to shift electric load"; 
																												}
																												elseif ($PotentialSurplusHours)
																												{
																																// SellSurplus ($miCreateSurplus = 2):																												  
																																$suggestion = $this->checkNewInterval($suggestion, $this::$miCreateSurplus, $hour); 	
																																$color_suggestion = "FFFFFF";
																																$message_hour = $options[$this::$miCreateSurplus]; // "Try to create a surplus";	
																												}																											
																												else
																												{
																															 // None ($miNone = 0):																												  
																																$suggestion = $this->checkNewInterval($suggestion, $this::$miNone, $hour); 	
																																$color_suggestion = "999999";
																																$message_hour = $options[$this::$miNone]; // "Try to create a surplus";	
																												}
																								}
																				}
																				elseif (strcmp($strategy, self::$peak) == 0) 
																				{
																								// Strategy 3: PEAK ---------------------------------------------------------------
																								
																								// suggestion values:
																								// 0: - (gray) $miNone
																								// 1: "Sell energy surplus" (green) $miSellSurplus
																								// 2: "Shift electric load here" (yeallow) $miShiftElecLoad
																								// 3: "Cover the energy surplus" (red) $miCoverSurplus
																								// 4: "Cover the energy surplus/Shift electric load here" (red) $miCoverSurplusShiftElecLoad
																								
																								// Inference RULE 1
																								
																								if (($production > $demand) &&	(!$priceCheck)) {
																												$message_hour = $options[$this::$miCoverSurplus];    // "Cover the energy surplus";			
																								}
																								else
																								{
																												if (($production > $demand) &&	($priceCheck)) {
																																$message_hour = $options[$this::$miSellSurplus]; // "Sell energy surplus";			
																												} else {
																																$message_hour = $options[$this::$miNone];        // "-";	
																												}
																								}
																								
																								// Inference RULE 2
																								$newValueEule2 = false;
																								if ($extraDemandSum < $energySurplusSum)
																								{
																											 // $suggestion = $this->checkNewInterval($suggestion, $this::$miNone, $hour); 
																												// $color_suggestion = "999999";
																												// $message_hour = $options[$this::$miNone]; // "-";	
																								}
																								else
																								{
																								    if( $message_hour == $options[$this::$miSellSurplus])
																												{
																												    // $suggestion = $this->checkNewInterval($suggestion, $this::$miNone, $hour); 	
																												    // $color_suggestion = "999999";
																												    // $message_hour = $options[$this::$miNone]; // "-";	
																												}
																												else
																												{
																								        $edemEgen = $demand - $production - $EnergySurplus;
																												
																												    //dump("**************************");
																												    //dump($edemEgen." < ".$PercentileEdem." && ".$purchase_price." < ".$Percentile." || ".$message_hour." == ".$options[$this::$miCoverSurplus]." && ".$purchase_price." < ".$Percentile);
																												
																												    if ( (($edemEgen < $PercentileEdem) && ($purchase_price < $Percentile) )  ||
																																     (($message_hour == $options[$this::$miCoverSurplus]) && ($purchase_price < $Percentile) )	)
																																{
																																    // Hypothesis (according to the Excel) -> if there is "Shift electric load here" in rule 2, 
																																			 // then rule 1 only can only have '-' or 'Cover the energy surplus' values.
																																
																																	   if($message_hour == $options[$this::$miNone])
																																				{
																																					   $suggestion = $this->checkNewInterval($suggestion, $this::$miShiftElecLoad, $hour); 
																																				    $color_suggestion = "EEEE99";
																																				    $message_hour = $options[$this::$miShiftElecLoad]; // "Shift electric load here" 
																																								$newValueEule2 = true;
																																				}
																																				else
																																				{
																																					   // REWRITE (combine with) Rule 1:
																																				    $suggestion = $this->checkNewInterval($suggestion, $this::$miCoverSurplusShiftElecLoad, $hour); 	
																																				    $color_suggestion = "EEEE99";
																																				    $message_hour = $options[$this::$miCoverSurplusShiftElecLoad]; // "Shift electric load here" 
																																								$newValueEule2 = true;
																																				}
																																}
																																else
																																{
																																				// $suggestion = $this->checkNewInterval($suggestion, $this::$miNone, $hour); 
																																			 // $color_suggestion = "999999";
																																			 // $message_hour = $options[$this::$miNone]; // "-";	
																																}
																												}
																								}
																								
																								if(!$newValueEule2)
																								{
																								    // if there is no new value in inference in rule 2, then check new values for rule 1:
																												
																								    if(	$message_hour == $options[$this::$miCoverSurplus])
																												{
																															$suggestion = $this->checkNewInterval($suggestion, $this::$miCoverSurplus, $hour); 
																															$color_suggestion = "FF9999";
																												}
																												else if(	$message_hour == $options[$this::$miSellSurplus])
																												{
																												    $suggestion = $this->checkNewInterval($suggestion, $this::$miSellSurplus, $hour); 	
																																$color_suggestion = "99FF99";
																												}
																												else if(	$message_hour == $options[$this::$miNone])
																												{
																													   $suggestion = $this->checkNewInterval($suggestion, $this::$miNone, $hour); 	
																																$color_suggestion = "999999";
																												}
																								}
																								
																								/*
																								
																								// The aim of this strategy is to analyse the opportunity to sell energy considering the self-using 
																								// of energy produced by PV system in order to cumulate a daily profit set in advance by the user.
																								
																								// Parameters fixed by the user:						
																								// 1. Dpenalty: It represent a target income to be reached by selling energy from PV.
																								//    Ex: 100.000 KW (deben consumirse estos KW o el cliente sale perdiendo)
																								
																								// 2. Residual energy: It is defined for each hour as the difference between the energy produced by PV and 
																								//    a % threshold (fixed by the user) of electrical energy demand. 
																								//    Ex: if threshold is 5%, and the energy provided by PV represents the 6% of energy demand, the residual energy is 1%. 
																								//        Edem = 9400 KW
																								//        Egen =  564 KW (=> 6% of Edem)
																								//        threshold = 5% (configuration)
																								//        $percentage_of_Edem = (9400 KW / 100) * 5% = 470 KW (=> 1% (94 KW) To sell)
																								
																								// 1. Calaculate % of Edem:	
																								$percentage_of_Edem = (($demand / 100)* $this->energy_demand_threshold_percentage); // ex: (9400 KW / 100) * 5% = 470 KW
																								
																								$Dpenalty = $this->daily_penalty_energy_threshold;
																								$hourlyCumulativeIncomePV = $hourlyCumulativeIncomePV + $demand;
																								
																								if(	$production > $percentage_of_Edem)	// KW
																								{
																									if($selling_price > $purchase_price)			// Price (per hour)
																									{
																										if($hourlyCumulativeIncomePV < $Dpenalty) {
																											$suggestion = $this->checkNewInterval($suggestion, $this::$miSellSurplus, $hour); 		// <-- "sell energy surplus" (GREEN)
																										} 
																										else  {
																											$suggestion = $this->checkNewInterval($suggestion, $this::$miAutoConsumption, $hour); 	// <-- "consume all the energy produced" (RED)
																										}						
																									}
																									else {
																										$suggestion = $this->checkNewInterval($suggestion, $this::$miAutoConsumption, $hour); 		// <-- "consume all the energy produced" (RED)
																									}
																								} 
																								else {
																									$suggestion = $this->checkNewInterval($suggestion, $this::$miAutoConsumption, $hour); 			// <-- "consume all the energy produced" (RED)
																								}		
																							}
																				*/
																				
																				}
																							
																				// HOUR values:
																				$aDataCalculation[]=array(	
																								"energy_price"=>$purchase_price, 
																								"energy_production"=>$production, 
																								"date"=>$register->getHour()->format("Y-m-d H:i:s"),
																								"suggestion"=>$suggestion,
																								"color_suggestion"=>$color_suggestion,
																								"energy_price_before"=>$purchase_price_before, 
																								"energy_production_before"=>2,
																								"message"=>$message_hour
																				);					
																				
																				// DAY values:					
																				$day_production = $day_production + $production/1000;					
																				$day_consumption = $day_consumption + $demand/1000;
																				$day_difference = $day_difference + ($production/1000 - $demand/1000);
																				
																				if($day_difference > 0) {
																					   $day_production_sold = $day_difference * $selling_price;
																				}
																				
																				//dump("ww ".$day_difference." * ".$selling_price." ".$day_production_sold);

																				// Problema -> Actualmente no tenemos con qué comparar is esto debe ser en realidad cost saving!!!!!		
																				$day_co2_emissions = $day_co2_emissions + ($day_difference*0.00001) ; // FAKE (pendiente de ALICE)
																				
																				$numRegisters++;	
															 }
																						
																$this->weekSoldProducedAcumulated = $this->weekSoldProducedAcumulated + $day_production_sold;				
																$this->weekEmissionsAcumulated = $this->weekEmissionsAcumulated + $day_co2_emissions;
												}
																
												$numIntervals = count($this->maDataIntervalDay);				
												//dump($this->maDataIntervalDay);
												if($numIntervals > 0)
												{
                // set the interval text (AA:BB-CC:DD)
													   for($p=0; $p< $numIntervals-1; $p++) 
																{
														      $this->maDataIntervalDay[$p]["hour"] = $this->maDataIntervalDay[$p]["hour"]."-".$this->maDataIntervalDay[$p+1]["hour"];
													   }				
													   $this->maDataIntervalDay[count($this->maDataIntervalDay)-1]["hour"] = $this->maDataIntervalDay[$p]["hour"]."-24:00";
												}																																			
																								
												// Num of intervals:
												// - 'groupView' corresponds with the number of stretch.
												// - Different intervals can be fall in the same stretch.
												//dump($this->maDataIntervalDay);
												if (is_array($this->maDataIntervalDay)) 
												{
													   foreach($this->maDataIntervalDay as $loIntervalDay)
													   {
																   //dump("groupView:".($loIntervalDay['groupView']+1)." > ".$maxIntervals);
														     if ($loIntervalDay['groupView'] + 1 > $maxIntervals ) {
															        $maxIntervals = $loIntervalDay['groupView'] + 1;
														     }				
													   }
						      }
												
												// if the last groupView is an empty interval -> remove it:
												$deleteStretch = true;
												if (is_array($this->maDataIntervalDay)) {
												    foreach($this->maDataIntervalDay as $loIntervalDay) {
																				if (($loIntervalDay['groupView'] == ($maxIntervals -1)) && ($loIntervalDay['type'] != 0)) {
																								$deleteStretch = false;
																				}
																}
												}												
												if(($maxIntervals > 0)&&($deleteStretch)){
												   $maxIntervals = $maxIntervals-1;
												}
										
									// Insert information of the current day into a global array (7 days):
									$aDataActionPlan[]=array("day"=>explode(" ", $loLstDays[$i])[0], 
														"calculation"=>$aDataCalculation, 		// values per each hour
														"options"=>$options,
														"day_production"=>$day_production,
														"day_consumption"=>$day_consumption,
														"day_difference"=>$day_difference,
														"day_production_sold"=>$day_production_sold,
														"day_co2_emissions"=>$day_co2_emissions,
														"purchase_price"=>$purchase_price,		// It takes the value of the last hour, but it's the same for all the day values.
														"selling_price"=>$selling_price,		// This is the same case
														"idCalculation"=>$idCalculation,
														"idOutputDay"=>$idOutputDay,
														"status"=>$status,
														"nameAbbreviatedDay"=>$nameAbbreviatedDay,
														"abbreviatedDay"=>$lsAbbreviatedDayFinal,
														"interval"=>$this->maDataIntervalDay,
														"maxIntervals"=>$maxIntervals,
														"strategy"=>$strategy); //change idStatus			
									}
									
		//dump($aDataActionPlan);									
		return $aDataActionPlan;
	}

    // Detect and add new interval limits of the day in a list
	private function checkNewInterval($oldSuggestion, $newSuggestion, $hour)
	{
        //dump($oldSuggestion." ".$newSuggestion);
		if($oldSuggestion != $newSuggestion)
		{
			$type = 0;
			foreach($this->maDataIntervalDay as $loIntervalDay)
			{
				if($loIntervalDay['type'] == $newSuggestion){
					$type++; 
				}				
			}												 
			   
			$this->maDataIntervalDay[]=array("hour"=>$hour, "type"=>$newSuggestion, "groupView"=>$type);												
			//dump($this->maDataIntervalDay);
		}		
	
		return $newSuggestion;
    }
				
	private function getPercentile($sequence, $excelPercentile)
	{
		sort($sequence);
		$N = sizeof($sequence);
		$n = ($N - 1) * $excelPercentile + 1;
		// Another method: double n = (N + 1) * excelPercentile;
		if ($n == 1) return $sequence[0];
		else if ($n == $N) return $sequence[$N - 1];
		else
		{
			$k = $n;
			$d = $n - $k;
			return $sequence[$k - 1] + $d * ($sequence[$k] - $sequence[$k - 1]);
		}
	}
	
	// Returns an array of every day between two dates:
	private function getDaysFromDate($from, $to)
	{
		$aDays=array();
		$aDays[0]=$from;
		for($i=1; $i<7; $i++)
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

	//GET Status & colors action plan	
	public function getTrafficLight($idActionPlan, $dateActual, $startDate, $endDate)
	{
		
		$initDay=$startDate->format("Y-m-d H:i:s");
		//$finalDay=$endDate->modify('-2 day')->format("Y-m-d H:i:s");
		$finalDay=$endDate->format("Y-m-d H:i:s");
		$actDay=$dateActual->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($initDay, $finalDay);		
		$numDays=count($aDays);								
		$aDataActionPlan=array();
		$aFinalValues=array();
				
		for($i=0; $i < $numDays; $i++)
		{
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			$currentDayFormat=explode(" ", $aDays[$i])[0];
			
			
			if($qCalculation != null)
			{
				$idCalculation=$qCalculation[0]->getId();			
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{					
					if($aDays[$i] < $actDay)
					{
						$status=$outputDay[0]->getStatus();
						if($status==0) 		$color="#ffff00";
						elseif($status==1)	$color="#00ff00";
						elseif($status==2)	$color="#ff0000";
						$aDataActionPlan[]=array("status"=>$color, "date"=>$aDays[$i]);			// 0=Unknown, 1=Accepted, 2=Declined 
					}elseif($aDays[$i] >= $actDay)
					{
						$status=$outputDay[0]->getStatus();
						if($status==0) 		$color="#cccccc";
						elseif($status==1)	$color="#00ff00";
						elseif($status==2)	$color="#ff0000";
						
						$aDataActionPlan[]=array("status"=>$color, "date"=>$aDays[$i]);
					}
				}else{					
					$aDataActionPlan[]=array("status"=>"#ffff00", "date"=>$aDays[$i]);
				}
			}else{				
				$aDataActionPlan[]=array("status"=>"#ffff00", "date"=>$aDays[$i]);
			}
		}
			
		$numUnk=$this->calculateUnknowns($aDataActionPlan, $dateActual);
		
		//if($numUnk == 0) 		$strStatus=0;
		if($numUnk >= 1)		$strStatus=1;
		else					$strStatus=2;
		
		$aFinalValues[]=array("aOutputActionPlan"=>$aDataActionPlan, "status"=>$strStatus);
		
		return $aFinalValues;
		
	}
	
	//Get number of unknows 
	private function calculateUnknowns($aDataActionPlan, $dateActual)
	{
		$actDay=$dateActual->format("Y-m-d");
		$numUnk=0;
		foreach($aDataActionPlan as $dayActionPlan)
		{			
			$currentDay=explode(" ", $dayActionPlan['date'])[0];
				
			if($currentDay >= $actDay and $dayActionPlan['status']=="#cccccc")		$numUnk++;			
		}
		
		return $numUnk;
	}

	//Get Status week 
	public function getStatusWeek($idActionPlan, $startDate, $endDate)
	{
		$initDay=$startDate." 00:00:00";
		$finalDay=\DateTime::createFromFormat('Y-m-d H:i:s', $endDate." 00:00:00")->modify("+1 day")->format("Y-m-d H:i:s");
		
		$aDays=$this->getDaysFromDate($initDay, $finalDay);		
		$numDays=count($aDays);
		$aStatusWeek=array();

		for($i=0; $i < $numDays; $i++)
		{
			$qCalculation=$this->em->getRepository('OptimusOptimusBundle:APCalculation')->findCalculationByDate($aDays[$i], $idActionPlan);
			$currentDayFormat=explode(" ", $aDays[$i])[0];
			
			
			if($qCalculation != null)
			{
				$idCalculation=$qCalculation[0]->getId();			
					
				$outputDay = $this->em->getRepository('OptimusOptimusBundle:APPVOutputDay')->findOutputByDay($idCalculation, $currentDayFormat); 
				
				if($outputDay)
				{
					$aStatusWeek[]=array('status'=>$outputDay[0]->getStatus(), 'idOutputDay'=>$outputDay[0]->getId());
					
				}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
				
			}else	$aStatusWeek[]=array('status'=>0, 'idOutputDay'=>0);
		}
		
		return $aStatusWeek;
	}

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
			
		$aHistorical=array(	"name"=>$apsensor->getFkSensor()->getName(), 
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
		
		$aPrediction=array("name"=>$apsensor->getFkSensor()->getName(), 
								"values"=>$aValues, 
								"color"=>$apsensor->getFkSensor()->getColor(),	
								"idSensor"=>$apsensor->getFkSensor()->getId(),
								"units"=>$apsensor->getFkSensor()->getUnits());
		
		return $aDataSensor=array("historical"=>$aHistorical, "prediction"=>$aPrediction);
	}
}
?>

 