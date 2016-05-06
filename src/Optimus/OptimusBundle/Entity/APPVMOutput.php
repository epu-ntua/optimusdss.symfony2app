<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APPVMOutput
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APPVMOutputRepository")
 */
class APPVMOutput
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
     * @var integer
     *
     * @ORM\Column(name="alarm_power", type="integer")
     */
	private $alarm_power;
	
	/**
     * @var float
     *
     * @ORM\Column(name="pvproduced", type="float")
     */
	private $pvproduced;
	
	/**
     * @var float
     *
     * @ORM\Column(name="pvpredicted", type="float")
     */
	private $pvpredicted;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="dayalert", type="integer")
     */
	private $dayalert;
	

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
     * Set alarm_power
     *
     * @param float $energyPrice
     * @return AP_PV_Output
     */
    public function setAlarmPower($alarm_power)
    {
        $this->alarm_power = $alarm_power;

        return $this;
    }

    /**
     * Get alarm_power
     *
     * @return float 
     */
    public function getAlarmPower()
    {
        return $this->alarm_power;
    }
	
	
	 /**
     * Set alarm_temperature
     *
     * @param float $alarm_temperature
     * @return AP_PV_Output
     */
    public function setAlarmTemperature($alarm_temperature)
    {
        $this->alarm_temperature = $alarm_temperature;

        return $this;
    }

    /**
     * Get alarm_temperature
     *
     * @return float 
     */
    public function getAlarmTemperature()
    {
        return $this->alarm_temperature;
    }

    /**
     * Set pvproduced_name
     *
     * @param float $pvproducedName
     * @return APPVMOutput
     */
    public function setPvproducedName($pvproducedName)
    {
        $this->pvproduced_name = $pvproducedName;

        return $this;
    }

    /**
     * Get pvproduced_name
     *
     * @return float 
     */
    public function getPvproducedName()
    {
        return $this->pvproduced_name;
    }

    /**
     * Set pvpredicted_name
     *
     * @param float $pvpredictedName
     * @return APPVMOutput
     */
    public function setPvpredictedName($pvpredictedName)
    {
        $this->pvpredicted_name = $pvpredictedName;

        return $this;
    }

    /**
     * Get pvpredicted_name
     *
     * @return float 
     */
    public function getPvpredictedName()
    {
        return $this->pvpredicted_name;
    }

    /**
     * Set pvproduced
     *
     * @param float $pvproduced
     * @return APPVMOutput
     */
    public function setPvproduced($pvproduced)
    {
        $this->pvproduced = $pvproduced;

        return $this;
    }

    /**
     * Get pvproduced
     *
     * @return float 
     */
    public function getPvproduced()
    {
        return $this->pvproduced;
    }

    /**
     * Set pvpredicted
     *
     * @param float $pvpredicted
     * @return APPVMOutput
     */
    public function setPvpredicted($pvpredicted)
    {
        $this->pvpredicted = $pvpredicted;

        return $this;
    }

    /**
     * Get pvpredicted
     *
     * @return float 
     */
    public function getPvpredicted()
    {
        return $this->pvpredicted;
    }
	
	/**
     * Set dayalert
     *
     * @param integer $dayalert
     * @return APPVMOutput
     */
    public function setDayalert($dayalert)
    {
        $this->dayalert = $dayalert;

        return $this;
    }

    /**
     * Get dayalert
     *
     * @return integer 
     */
    public function getDayalert()
    {
        return $this->dayalert;
    }
}
