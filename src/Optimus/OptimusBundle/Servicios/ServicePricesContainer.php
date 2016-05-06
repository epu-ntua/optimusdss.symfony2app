<?php

namespace Optimus\OptimusBundle\Servicios;

use Optimus\OptimusBundle\Servicios\ServiceDailyPrices;
use Optimus\OptimusBundle\Servicios\ServiceTariffType;

class ServicePricesContainer { 
	
	private $startDate;
	private $endDate;
	private $tariffType;
	private $dailyPrices;   
 
 
	public function __construct(ServiceDailyPrices $dailyPrices,  ServiceTariffType $tariffType)
    {
        $this->dailyPrices=$dailyPrices;
		$this->tariffType=$tariffType;
    }
	
	public function prevInit($type,$periods,$date1,$date2)
	{
		$this->tariffType->init($type,$periods);
		
		$this->startDate  = \DateTime::createFromFormat("Y-m-d" ,$date1)->format('Y-m-d');
		
		$this->endDate    = \DateTime::createFromFormat("Y-m-d" ,$date2)->format('Y-m-d');		    
	}
	
	public function initialize ($data)
	{
		$this->dailyPrices->setPriceList($data);
	}
	
	public function  getDailyPrices ()
	{
		return $this->dailyPrices;
	}
	
	public function  getTariffType ()
	{
		return $this->tariffType;
	}
	
	public function  getStartDate ()
	{
		return $this->startDate;
	}
	
}

?>