<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AP_Sensors
 *
 * @ORM\Table()
 * @ORM\Entity (repositoryClass="Optimus\OptimusBundle\Repository\APSensorsRepository")
 */
class APSensors
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
     * @ORM\ManyToOne(targetEntity="ActionPlans")
     * @ORM\JoinColumn(name="fk_actionplan", referencedColumnName="id", onDelete="CASCADE")
     */ 
    protected $fk_actionplan;
	
	/**
     * @ORM\ManyToOne(targetEntity="Sensor")
     * @ORM\JoinColumn(name="fk_sensor", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_sensor;
	
	/**
     * @ORM\ManyToOne(targetEntity="BuildingPartitioning")
     * @ORM\JoinColumn(name="fk_BuildingPartitioning", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_BuildingPartitioning;

	/**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
	/**
     * @var integer
     *
     * @ORM\Column(name="orderSensor", type="integer")
     */
    private $orderSensor;
	

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
     * Set fk_actionplan
     *
     * @param \Optimus\OptimusBundle\Entity\ActionPlans $fkActionplan
     * @return AP_Sensors
     */
    public function setFkActionplan(\Optimus\OptimusBundle\Entity\ActionPlans $fkActionplan = null)
    {
        $this->fk_actionplan = $fkActionplan;

        return $this;
    }

    /**
     * Get fk_actionplan
     *
     * @return \Optimus\OptimusBundle\Entity\ActionPlans 
     */
    public function getFkActionplan()
    {
        return $this->fk_actionplan;
    }

    /**
     * Set fk_sensor
     *
     * @param \Optimus\OptimusBundle\Entity\Sensor $fkSensor
     * @return AP_Sensors
     */
    public function setFkSensor(\Optimus\OptimusBundle\Entity\Sensor $fkSensor = null)
    {
        $this->fk_sensor = $fkSensor;

        return $this;
    }

    /**
     * Get fk_sensor
     *
     * @return \Optimus\OptimusBundle\Entity\Sensor 
     */
    public function getFkSensor()
    {
        return $this->fk_sensor;
    }

    /**
     * Set fk_BuildingPartitioning
     *
     * @param \Optimus\OptimusBundle\Entity\BuildingPartitioning $fkBuildingPartitioning
     * @return APSensors
     */
    public function setFkBuildingPartitioning(\Optimus\OptimusBundle\Entity\BuildingPartitioning $fkBuildingPartitioning = null)
    {
        $this->fk_BuildingPartitioning = $fkBuildingPartitioning;

        return $this;
    }

    /**
     * Get fk_BuildingPartitioning
     *
     * @return \Optimus\OptimusBundle\Entity\BuildingPartitioning 
     */
    public function getFkBuildingPartitioning()
    {
        return $this->fk_BuildingPartitioning;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return APSensors
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
     * Set orderSensor
     *
     * @param integer $orderSensor
     * @return APSensors
     */
    public function setOrderSensor($orderSensor)
    {
        $this->orderSensor = $orderSensor;

        return $this;
    }

    /**
     * Get orderSensor
     *
     * @return integer 
     */
    public function getOrderSensor()
    {
        return $this->orderSensor;
    }
}
