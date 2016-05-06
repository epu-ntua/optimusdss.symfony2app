<?php

namespace Optimus\OptimusBundle\Servicios;

class ServiceDailyPrices { 
	
	private $prices;   
 
 
	public function resetPriceList ()
	{
		unset ($prices);
		$this->prices = array();
	}

	public function getPrice ($idx)
	{
		return $this->prices[$idx];
	}

	public function getPriceList () 
	{
		return $this->prices;
	}

	public function setPrice ($item)
	{
		$this->prices[] = $item;
	}

	public function setPriceList ($items)
	{
		$this->resetPriceList();
		$this->prices= $items;
	}
	
}

?>