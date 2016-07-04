<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tracker
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tracker
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
     * @var float
     *
     * @ORM\Column(name="target_EConsumption", type="float")
     */
    private $targetEConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="potential_EConsumption", type="float")
     */
    private $potentialEConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="target_CO2", type="float")
     */
    private $targetCO2;

    /**
     * @var float
     *
     * @ORM\Column(name="potential_CO2", type="float")
     */
    private $potentialCO2;

    /**
     * @var float
     *
     * @ORM\Column(name="target_ECost", type="float")
     */
    private $targetECost;

    /**
     * @var float
     *
     * @ORM\Column(name="potential_ECost", type="float")
     */
    private $potentialECost;

    /**
     * @var float
     *
     * @ORM\Column(name="target_REUse", type="float")
     */
    private $targetREUse;

    /**
     * @var float
     *
     * @ORM\Column(name="potential_REUse", type="float")
     */
    private $potentialREUse;

    /**
     * @var float
     *
     * @ORM\Column(name="baseline_EConsumption", type="float")
     */
    private $baselineEConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="baseline_CO2", type="float")
     */
    private $baselineCO2;

    /**
     * @var float
     *
     * @ORM\Column(name="baseline_ECost", type="float")
     */
    private $baselineECost;

    /**
     * @var float
     *
     * @ORM\Column(name="baseline_REUse", type="float")
     */
    private $baselineREUse;


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
     * Set targetEConsumption
     *
     * @param float $targetEConsumption
     * @return Tracker
     */
    public function setTargetEConsumption($targetEConsumption)
    {
        $this->targetEConsumption = $targetEConsumption;
    
        return $this;
    }

    /**
     * Get targetEConsumption
     *
     * @return float 
     */
    public function getTargetEConsumption()
    {
        return $this->targetEConsumption;
    }

    /**
     * Set potentialEConsumption
     *
     * @param float $potentialEConsumption
     * @return Tracker
     */
    public function setPotentialEConsumption($potentialEConsumption)
    {
        $this->potentialEConsumption = $potentialEConsumption;
    
        return $this;
    }

    /**
     * Get potentialEConsumption
     *
     * @return float 
     */
    public function getPotentialEConsumption()
    {
        return $this->potentialEConsumption;
    }

    /**
     * Set targetCO2
     *
     * @param float $targetCO2
     * @return Tracker
     */
    public function setTargetCO2($targetCO2)
    {
        $this->targetCO2 = $targetCO2;
    
        return $this;
    }

    /**
     * Get targetCO2
     *
     * @return float 
     */
    public function getTargetCO2()
    {
        return $this->targetCO2;
    }

    /**
     * Set potentialCO2
     *
     * @param float $potentialCO2
     * @return Tracker
     */
    public function setPotentialCO2($potentialCO2)
    {
        $this->potentialCO2 = $potentialCO2;
    
        return $this;
    }

    /**
     * Get potentialCO2
     *
     * @return float 
     */
    public function getPotentialCO2()
    {
        return $this->potentialCO2;
    }

    /**
     * Set targetECost
     *
     * @param float $targetECost
     * @return Tracker
     */
    public function setTargetECost($targetECost)
    {
        $this->targetECost = $targetECost;
    
        return $this;
    }

    /**
     * Get targetECost
     *
     * @return float 
     */
    public function getTargetECost()
    {
        return $this->targetECost;
    }

    /**
     * Set potentialECost
     *
     * @param float $potentialECost
     * @return Tracker
     */
    public function setPotentialECost($potentialECost)
    {
        $this->potentialECost = $potentialECost;
    
        return $this;
    }

    /**
     * Get potentialECost
     *
     * @return float 
     */
    public function getPotentialECost()
    {
        return $this->potentialECost;
    }

    /**
     * Set targetREUse
     *
     * @param float $targetREUse
     * @return Tracker
     */
    public function setTargetREUse($targetREUse)
    {
        $this->targetREUse = $targetREUse;
    
        return $this;
    }

    /**
     * Get targetREUse
     *
     * @return float 
     */
    public function getTargetREUse()
    {
        return $this->targetREUse;
    }

    /**
     * Set potentialREUse
     *
     * @param float $potentialREUse
     * @return Tracker
     */
    public function setPotentialREUse($potentialREUse)
    {
        $this->potentialREUse = $potentialREUse;
    
        return $this;
    }

    /**
     * Get potentialREUse
     *
     * @return float 
     */
    public function getPotentialREUse()
    {
        return $this->potentialREUse;
    }

    /**
     * Set baselineEConsumption
     *
     * @param float $baselineEConsumption
     * @return Tracker
     */
    public function setBaselineEConsumption($baselineEConsumption)
    {
        $this->baselineEConsumption = $baselineEConsumption;
    
        return $this;
    }

    /**
     * Get baselineEConsumption
     *
     * @return float 
     */
    public function getBaselineEConsumption()
    {
        return $this->baselineEConsumption;
    }

    /**
     * Set baselineCO2
     *
     * @param float $baselineCO2
     * @return Tracker
     */
    public function setBaselineCO2($baselineCO2)
    {
        $this->baselineCO2 = $baselineCO2;
    
        return $this;
    }

    /**
     * Get baselineCO2
     *
     * @return float 
     */
    public function getBaselineCO2()
    {
        return $this->baselineCO2;
    }

    /**
     * Set baselineECost
     *
     * @param float $baselineECost
     * @return Tracker
     */
    public function setBaselineECost($baselineECost)
    {
        $this->baselineECost = $baselineECost;
    
        return $this;
    }

    /**
     * Get baselineECost
     *
     * @return float 
     */
    public function getBaselineECost()
    {
        return $this->baselineECost;
    }

    /**
     * Set baselineREUse
     *
     * @param float $baselineREUse
     * @return Tracker
     */
    public function setBaselineREUse($baselineREUse)
    {
        $this->baselineREUse = $baselineREUse;
    
        return $this;
    }

    /**
     * Get baselineREUse
     *
     * @return float 
     */
    public function getBaselineREUse()
    {
        return $this->baselineREUse;
    }
}
