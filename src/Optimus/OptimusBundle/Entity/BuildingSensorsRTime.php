<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingSensorsRTime
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BuildingSensorsRTime
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
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="fk_Building", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_Building;

    /**
     * @ORM\ManyToOne(targetEntity="Sensor")
     * @ORM\JoinColumn(name="fk_sensor", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_sensor;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set fkBuilding
     *
     * @param \Optimus\OptimusBundle\Entity\Building $fkBuilding
     * @return BuildingPartitioning
     */
    public function setFkBuilding(\Optimus\OptimusBundle\Entity\Building $fkBuilding = null)
    {
        $this->fk_Building = $fkBuilding;

        return $this;
    }

    /**
     * Get fkBuilding
     *
     * @return integer 
     */
    public function getFkBuilding()
    {
        return $this->fk_Building;
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
     * Get fkSensor
     *
     * @return integer 
     */
    public function getFkSensor()
    {
        return $this->fk_sensor;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BuildingSensorsRTime
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
}
