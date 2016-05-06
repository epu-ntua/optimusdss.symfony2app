<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensor
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\SensorRepository")
 */
class Sensor
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=7, options={"default":""})
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="display", type="string", length=255, options={"default":""})
     */
    private $display;
	
	/**
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="fk_Building", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_Building;
	
	/**
     * @var string
     *
     * @ORM\Column(name="urlService", type="string", length=255, options={"default":""})
     */
	private $urlService;
	
	/**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, options={"default":""})
     */
	private $status;
	
	/**
     * @var string
     *
     * @ORM\Column(name="lastData", type="string", length=255)
     */
	private $lastData;
	
	/**
     * @var string
     *
     * @ORM\Column(name="description", type="text", options={"default":""})
     */
	private $description;
    
		/**
     * @var string
     *
     * @ORM\Column(name="units", type="string", length=255, options={"default":""})
     */
	private $units;
	
	/**
     * @var string
     *
     * @ORM\Column(name="predictionmodelparameters", type="string", length=255)
     */
	private $predictionmodelparameters;
	
		/**
     * @var string
     *
     * @ORM\Column(name="aggregation", type="string", length=255)
     */
	private $aggregation;
	
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
     * @return Sensor
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
     * Set url
     *
     * @param string $url
     * @return Sensor
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Sensor
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set display
     *
     * @param string $display
     * @return Sensor
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display
     *
     * @return string 
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Set fk_Building
     *
     * @param \Optimus\OptimusBundle\Entity\Building $fkBuilding
     * @return Sensor
     */
    public function setFkBuilding(\Optimus\OptimusBundle\Entity\Building $fkBuilding = null)
    {
        $this->fk_Building = $fkBuilding;

        return $this;
    }

    /**
     * Get fk_Building
     *
     * @return \Optimus\OptimusBundle\Entity\Building 
     */
    public function getFkBuilding()
    {
        return $this->fk_Building;
    }

    /**
     * Set urlService
     *
     * @param string $urlService
     * @return Sensor
     */
    public function setUrlService($urlService)
    {
        $this->urlService = $urlService;

        return $this;
    }

    /**
     * Get urlService
     *
     * @return string 
     */
    public function getUrlService()
    {
        return $this->urlService;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Sensor
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lastData
     *
     * @param string $lastData
     * @return Sensor
     */
    public function setLastData($lastData)
    {
        $this->lastData = $lastData;

        return $this;
    }

    /**
     * Get lastData
     *
     * @return string 
     */
    public function getLastData()
    {
        return $this->lastData;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Sensor
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set units
     *
     * @param string $units
     * @return Sensor
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * Get units
     *
     * @return string 
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * Set predictionmodelparameters
     *
     * @param string $predictionmodelparameters
     * @return Sensor
     */
    public function setPredictionmodelparameters($predictionmodelparameters)
    {
        $this->predictionmodelparameters = $predictionmodelparameters;

        return $this;
    }

    /**
     * Get predictionmodelparameters
     *
     * @return string 
     */
    public function getPredictionmodelparameters()
    {
        return $this->predictionmodelparameters;
    }

    /**
     * Set aggregation
     *
     * @param string $aggregation
     * @return Sensor
     */
    public function setAggregation($aggregation)
    {
        $this->aggregation = $aggregation;

        return $this;
    }

    /**
     * Get aggregation
     *
     * @return string 
     */
    public function getAggregation()
    {
        return $this->aggregation;
    }
}
