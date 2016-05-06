<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AP_PV_Output
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APPVOutputRepository")
 */
class APPVOutput
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
	 * @ORM\ManyToOne(targetEntity="APCalculation")
	 * @ORM\JoinColumn(name="fk_ap_calculation", referencedColumnName="id", onDelete="CASCADE")
	 */
    protected $fkApCalculation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hour", type="datetime")
     */
    private $hour;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_price", type="float")
     */
    private $energyPrice;
	
	/**
     * @var float
     *
     * @ORM\Column(name="energy_price_selling", type="float")
     */
    private $energyPriceSelling;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_production", type="float")
     */
    private $energyProduction;

	 /**
     * @var float
     *
     * @ORM\Column(name="consumption", type="float")
     */
    private $consumption;
	

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
     * Set hour
     *
     * @param \DateTime $hour
     * @return AP_PV_Output
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return \DateTime 
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set energyPrice
     *
     * @param float $energyPrice
     * @return AP_PV_Output
     */
    public function setEnergyPrice($energyPrice)
    {
        $this->energyPrice = $energyPrice;

        return $this;
    }

    /**
     * Get energyPrice
     *
     * @return float 
     */
    public function getEnergyPrice()
    {
        return $this->energyPrice;
    }
	
	
	/**
     * Set sellingEnergyPrice
     *
     * @param float $sellingEnergyPrice
     * @return AP_PV_Output
     */
    public function setEnergyPriceSelling($energyPriceSelling)
    {
        $this->energyPriceSelling = $energyPriceSelling;

        return $this;
    }

    /**
     * Get sellingEnergyPrice
     *
     * @return float 
     */
    public function getEnergyPriceSelling()
    {
        return $this->energyPriceSelling;
    }

    /**
     * Set energyProduction
     *
     * @param float $energyProduction
     * @return AP_PV_Output
     */
    public function setEnergyProduction($energyProduction)
    {
        $this->energyProduction = $energyProduction;

        return $this;
    }

    /**
     * Get energyProduction
     *
     * @return float 
     */
    public function getEnergyProduction()
    {
        return $this->energyProduction;
    }
	
	 /**
     * Get energyProduction
     *
     * @return float 
     */
    public function setEnergyConsumption($consumption)
    {
        $this->consumption = $consumption;
    }
	
	 /**
     * Get energyProduction
     *
     * @return float 
     */
    public function getEnergyConsumption()
    {
        return $this->consumption;
    }

    /**
     * Set fkApCalculation
     *
     * @param \Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation
     * @return AP_PV_Output
     */
    public function setFkApCalculation(\Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation = null)
    {
        $this->fkApCalculation = $fkApCalculation;

        return $this;
    }

    /**
     * Get fkApCalculation
     *
     * @return \Optimus\OptimusBundle\Entity\APCalculation 
     */
    public function getFkApCalculation()
    {
        return $this->fkApCalculation;
    }
}
