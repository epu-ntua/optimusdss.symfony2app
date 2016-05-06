<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Building
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Building
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;
	
	/**
     * @var string
     *
     * @ORM\Column(name="use_building", type="string", length=255)
     */
    private $use_building;
	
	/**
     * @var string
     *
     * @ORM\Column(name="year_of_construction", type="string", length=4)
     */
    private $year_of_construction;
	
	/**
     * @var string
     *
     * @ORM\Column(name="surface", type="string", length=255)
     */
    private $surface;
	
	/**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=255)
     */
    private $occupation;
	
	/**
     * @var string
     *
     * @ORM\Column(name="energy_rating", type="string", length=255)
     */
    private $energy_rating;
	
	/**
     * @var string
     *
     * @ORM\Column(name="electricity_consumption", type="string", length=255)
     */
    private $electricity_consumption;
	
	/**
     * @var string
     *
     * @ORM\Column(name="gas_consumption", type="string", length=255)
     */
    private $gas_consumption;
	
	/**
     * @var string
     *
     * @ORM\Column(name="energy_production_from_RES", type="string", length=255)
     */
    private $energy_production_from_RES;
	
	/**
     * @var string
     *
     * @ORM\Column(name="electricity_energy_cost", type="string", length=255)
     */
    private $electricity_energy_cost;
	
	/**
     * @var string
     *
     * @ORM\Column(name="gas_energy_cost", type="string", length=255)
     */
    private $gas_energy_cost;
	

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Building
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Building
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Building
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set use
     *
     * @param string $use
     * @return Building
     */
    public function setUse($use)
    {
        $this->use = $use;

        return $this;
    }

    /**
     * Get use
     *
     * @return string 
     */
    public function getUse()
    {
        return $this->use;
    }

    /**
     * Set year_of_construction
     *
     * @param integer $yearOfConstruction
     * @return Building
     */
    public function setYearOfConstruction($yearOfConstruction)
    {
        $this->year_of_construction = $yearOfConstruction;

        return $this;
    }

    /**
     * Get year_of_construction
     *
     * @return integer 
     */
    public function getYearOfConstruction()
    {
        return $this->year_of_construction;
    }

    /**
     * Set surface
     *
     * @param string $surface
     * @return Building
     */
    public function setSurface($surface)
    {
        $this->surface = $surface;

        return $this;
    }

    /**
     * Get surface
     *
     * @return string 
     */
    public function getSurface()
    {
        return $this->surface;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return Building
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     *
     * @return string 
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set use_building
     *
     * @param string $useBuilding
     * @return Building
     */
    public function setUseBuilding($useBuilding)
    {
        $this->use_building = $useBuilding;

        return $this;
    }

    /**
     * Get use_building
     *
     * @return string 
     */
    public function getUseBuilding()
    {
        return $this->use_building;
    }
    
    /**
     * Set energy_rating
     *
     * @param string $energyRating
     * @return Building
     */
    public function setEnergyRating($energyRating)
    {
        $this->energy_rating = $energyRating;

        return $this;
    }

    /**
     * Get energy_rating
     *
     * @return string 
     */
    public function getEnergyRating()
    {
        return $this->energy_rating;
    }

    /**
     * Set electricity_consumption
     *
     * @param string $electricityConsumption
     * @return Building
     */
    public function setElectricityConsumption($electricityConsumption)
    {
        $this->electricity_consumption = $electricityConsumption;

        return $this;
    }

    /**
     * Get electricity_consumption
     *
     * @return string 
     */
    public function getElectricityConsumption()
    {
        return $this->electricity_consumption;
    }

    /**
     * Set gas_consumption
     *
     * @param string $gasConsumption
     * @return Building
     */
    public function setGasConsumption($gasConsumption)
    {
        $this->gas_consumption = $gasConsumption;

        return $this;
    }

    /**
     * Get gas_consumption
     *
     * @return string 
     */
    public function getGasConsumption()
    {
        return $this->gas_consumption;
    }

    /**
     * Set energy_production_from_RES
     *
     * @param string $energyProductionFromRES
     * @return Building
     */
    public function setEnergyProductionFromRES($energyProductionFromRES)
    {
        $this->energy_production_from_RES = $energyProductionFromRES;

        return $this;
    }

    /**
     * Get energy_production_from_RES
     *
     * @return string 
     */
    public function getEnergyProductionFromRES()
    {
        return $this->energy_production_from_RES;
    }

    /**
     * Set electricity_energy_cost
     *
     * @param string $electricityEnergyCost
     * @return Building
     */
    public function setElectricityEnergyCost($electricityEnergyCost)
    {
        $this->electricity_energy_cost = $electricityEnergyCost;

        return $this;
    }

    /**
     * Get electricity_energy_cost
     *
     * @return string 
     */
    public function getElectricityEnergyCost()
    {
        return $this->electricity_energy_cost;
    }

    /**
     * Set gas_energy_cost
     *
     * @param string $gasEnergyCost
     * @return Building
     */
    public function setGasEnergyCost($gasEnergyCost)
    {
        $this->gas_energy_cost = $gasEnergyCost;

        return $this;
    }

    /**
     * Get gas_energy_cost
     *
     * @return string 
     */
    public function getGasEnergyCost()
    {
        return $this->gas_energy_cost;
    }
}
