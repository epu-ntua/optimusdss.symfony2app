<?php

namespace Optimus\OptimusBundle\Servicios;

use Doctrine\ORM\EntityManager;
use Optimus\OptimusBundle\Servicios\ServiceDataCapturing;

class ServicePricePredictor { 
	
	protected $dataCapturing;
	protected $periodEvaluator;
 
    protected $em;
	
 
    public function __construct(ServiceDataCapturing $dataCapturing,ServicePeriodEvaluator $periodEvaluator,  EntityManager $em)
    {
        $this->dataCapturing=$dataCapturing;
        $this->periodEvaluator=$periodEvaluator;
		$this->em=$em;
    }
	
	/**
	 * @param 
	 *
	 * $actualDate 			date actual at this moment
	 * $daySelected			el primer dia seleccionado a consultar
	 * $finalDay			El último día que se quiere consultar
	 * $nameSensor			Name del sensor (energy production, temperature, energy price...)
	 * $type				Type a consultar
	 * $idBuilding			Identificador del edificio
	 */
	public function getPricePredictor($from='2015-06-22 00:00:00', $to='2015-07-15 00:00:00', $nameSensor='energy price', $type='variable', $idBuilding)
	{
       // dump("From: ".$from."<br>");
       // dump("to: ".$to."<br>");
        
		//call to function historic data
		$dataHistoric=$this->dataCapturing->getDataFromDate(date("Y-m-d H:i:s"), $from, $to, $nameSensor, 'variable' , $idBuilding );
		
		if(count($dataHistoric) > 0)
		{
           // dump("Historic data: <br>");
            //dump($dataHistoric);
           
            
			$monthData=$this->createDataEnter($dataHistoric,  $from, $to);
			
			//dump(" -- Month data -- ");
			//dump($monthData);
						
			//$startdate = \DateTime::createFromFormat("Y-m-d H:i:s" , "2015-08-15 00:00:00")->format('Y-m-d'); //$from
			//$enddate = \DateTime::createFromFormat("Y-m-d H:i:s" , "2015-09-15 00:00:00")->format('Y-m-d'); //$to

			$startdate = \DateTime::createFromFormat("Y-m-d H:i:s" , $from)->format('Y-m-d'); //$from
			$enddate = \DateTime::createFromFormat("Y-m-d H:i:s" , $to)->format('Y-m-d'); //$to
			
			
			$this->periodEvaluator->getPriceContainer()->prevInit("P6", "4", $startdate, $enddate);
			
			$this->periodEvaluator->getPriceContainer()->initialize($monthData);
			
			$this->periodEvaluator->Evaluate();
			
			$avgDataDayType = $this->periodEvaluator->ExpandAveragePrices();
			
			//dump("--------------------- Avg DataDay Type ----------------------");
			//dump($avgDataDayType);
			//dump("------------------------------------------------------------");
			
			//TECNALIA Added new return variable.
			$weekAheadPrices = $this->periodEvaluator->GetNextWeekForecast();
			//dump("--------------------- Price Predictor ----------------------");
			//dump($weekAheadPrices);
			//dump("------------------------------------------------------------");
            
            //return $avgDataDayType;
			return $weekAheadPrices;
			
		}	
		
        return array();
	}	
	
	function createDataEnter($monthData, $from, $to)
	{
		$aDays=$this->dataCapturing->getDaysFromDate($from, $to, 'open');
		
		$aData=array();
		$count=0;
		
		//dump($aDays);
		foreach($aDays as $day)
		{
			$date 	= \DateTime::createFromFormat("Y-m-d H:i:s", $day)->format("Y-m-d");
			
			for($h=0; $h<24; $h++)
			{
				$equals=false;
				$value=0;
				
				if($h<10) $hour="0".$h.":00:00"; 
				else $hour=$h.":00:00";
				
				$dayActual=\DateTime::createFromFormat("Y-m-d H:i:s", $date." ".$hour);
				
				//dump($dayActual);
                
				for($i=0; $i<count($monthData[0]['values']); $i++)
				{
                    //if($h < 1 && $i < 2) {
                    
                    $dateValue = \DateTime::createFromFormat("Y-m-d H:i:s", $monthData[0]['values'][$i]['date']->format("Y-m-d H:i:s"));
                    //dump($monthData[0]['values'][$i]['date']);
                    // dump($dateValue);
                    //}
					//Presuponiendo que siempre son 00:00, 01:00, 02:00 ...					
					if($dateValue==$dayActual)
					{
						$value=$monthData[0]['values'][$i]['value'];
						//$value=rand(24,57);
						$equals=true;
					}					
				}
				
				if($equals==true)
				{
					$aData[$count]=($value);
				}else $aData[$count]=$value;
				
				$count++;
			}
			
		}

		return $aData;
	}
}

?>