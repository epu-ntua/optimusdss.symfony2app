<?php

namespace Optimus\OptimusBundle\Servicios;

class ServiceTariffType { 
	
	private $type;
	private $numberOfPeriods;
	private $periodDuration;    
 
 
	public function init($a,$b)
	{
		$this->type = $a;
		$this->numberOfPeriods=$b;
		$this->periodDuration = array(
			"w" => array(0,3,15,23,24),
			"h" => array(0,6,18,23,24)
		);
	}
	
	public function getPeriodsByType ($type)
	{
		return $this->periodDuration[$type];
	}
	  
	public function setType ($a)
	{
		$this->type = $a;
	}
	public function setPeriods ($a)
	{
		$this->numberOfPeriods = $a;
	}
	public function getType ()
	{
		return $this->type;
	}
	public function getPeriods ()
	{
		return $this->periods;
	}
	public function resetPeriodDuration ()
	{
		unset ($this->periodDuration);
		$this->periodDuration= array();
	}
	public function setPeriodDuration ($start, $end)
	{
		$this->periodDuration[$start] = $end;
	}
	
}

?>