<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APSwitchOutput
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APSwitchOutputRepository")
 */
class APSwitchOutput
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
     * @var \string
     *
     * @ORM\Column(name="start", type="string", length=255)
     */
    private $start;

    /**
     * @var \string
     *
     * @ORM\Column(name="stop", type="string", length=255)
     */
    private $stop;

    /**
     * @var \date
     *
     * @ORM\Column(name="day", type="date")
     */
    private $day;
	
	 /**
     * @var \float
     *
     * @ORM\Column(name="setpoint", type="float")
     */
    private $setpoint;
	

    /**
	 * @ORM\ManyToOne(targetEntity="APCalculation")
	 * @ORM\JoinColumn(name="fk_ap_calculation", referencedColumnName="id", onDelete="CASCADE")
	 */
    protected $fkApCalculation;

	/**
     * @ORM\ManyToOne(targetEntity="BuildingPartitioning")
     * @ORM\JoinColumn(name="fk_BuildingPartitioning", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_BuildingPartitioning;

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
     * Set typeSensor
     *
     * @param string $typeSensor
     * @return APSwitchOutput
     */
    public function setTypeSensor($typeSensor)
    {
        $this->typeSensor = $typeSensor;

        return $this;
    }

    /**
     * Get typeSensor
     *
     * @return string 
     */
    public function getTypeSensor()
    {
        return $this->typeSensor;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return APSwitchOutput
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set stop
     *
     * @param \DateTime $stop
     * @return APSwitchOutput
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop
     *
     * @return \DateTime 
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * Set day
     *
     * @param \DateTime $day
     * @return APSwitchOutput
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return \DateTime 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set fkApCalculation
     *
     * @param integer $fkApCalculation
     * @return APSwitchOutput
     */
    public function setFkApCalculation(\Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation = null)
    {
        $this->fkApCalculation = $fkApCalculation;

        return $this;
    }

    /**
     * Get fkApCalculation
     *
     * @return integer 
     */
    public function getFkApCalculation()
    {
        return $this->fkApCalculation;
    }

    /**
     * Set fk_BuildingPartitioning
     *
     * @param \Optimus\OptimusBundle\Entity\BuildingPartitioning $fkBuildingPartitioning
     * @return APSwitchOutput
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
     * Set setpoint
     *
     * @param float $setpoint
     * @return APSwitchOutput
     */
    public function setSetpoint($setpoint)
    {
        $this->setpoint = $setpoint;

        return $this;
    }

    /**
     * Get setpoint
     *
     * @return float 
     */
    public function getSetpoint()
    {
        return $this->setpoint;
    }
}
