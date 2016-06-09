<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CurrentStatus
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CurrentStatus
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="status_EConsumption", type="float")
     */
    private $statusEConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="status_CO2", type="float")
     */
    private $statusCO2;

    /**
     * @var float
     *
     * @ORM\Column(name="status_ECost", type="float")
     */
    private $statusECost;

    /**
     * @var float
     *
     * @ORM\Column(name="status_REUse", type="float")
     */
    private $statusREUse;


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
     * Set date
     *
     * @param \DateTime $date
     * @return CurrentStatus
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set statusEConsumption
     *
     * @param float $statusEConsumption
     * @return CurrentStatus
     */
    public function setStatusEConsumption($statusEConsumption)
    {
        $this->statusEConsumption = $statusEConsumption;
    
        return $this;
    }

    /**
     * Get statusEConsumption
     *
     * @return float 
     */
    public function getStatusEConsumption()
    {
        return $this->statusEConsumption;
    }

    /**
     * Set statusCO2
     *
     * @param float $statusCO2
     * @return CurrentStatus
     */
    public function setStatusCO2($statusCO2)
    {
        $this->statusCO2 = $statusCO2;
    
        return $this;
    }

    /**
     * Get statusCO2
     *
     * @return float 
     */
    public function getStatusCO2()
    {
        return $this->statusCO2;
    }

    /**
     * Set statusECost
     *
     * @param float $statusECost
     * @return CurrentStatus
     */
    public function setStatusECost($statusECost)
    {
        $this->statusECost = $statusECost;
    
        return $this;
    }

    /**
     * Get statusECost
     *
     * @return float 
     */
    public function getStatusECost()
    {
        return $this->statusECost;
    }

    /**
     * Set statusREUse
     *
     * @param float $statusREUse
     * @return CurrentStatus
     */
    public function setStatusREUse($statusREUse)
    {
        $this->statusREUse = $statusREUse;
    
        return $this;
    }

    /**
     * Get statusREUse
     *
     * @return float 
     */
    public function getStatusREUse()
    {
        return $this->statusREUse;
    }
}
