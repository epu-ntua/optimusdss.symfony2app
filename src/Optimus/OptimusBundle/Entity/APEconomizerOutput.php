<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APEconomizerOutput
 *
 * @ORM\Table(name="APEconOutput")
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APEconomizerOutputRepository")
 */
class APEconomizerOutput
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
     * @ORM\Column(name="temp_external", type="float")
     */
	private $temp_external;
	
		/**
     * @var float
     *
     * @ORM\Column(name="humidity", type="float")
     */
	private $humidity;
	

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
     * @return AP_Economizer_Output
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
     * @return AP_Economizer_Output
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
     * Set temp_external
     *
     * @param float $pvproduced
     * @return APEconomizerOutput
     */
    public function setTemp_external($temp_external)
    {
        $this->temp_external = $temp_external;

        return $this;
    }

    /**
     * Get temp_external
     *
     * @return float 
     */
    public function getTemp_external()
    {
        return $this->temp_external;
    }

    /**
     * Set humidity
     *
     * @param float $humidity
     * @return APPVMOutput
     */
    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;

        return $this;
    }

    /**
     * Get humidity
     *
     * @return float 
     */
    public function getHumidity()
    {
        return $this->humidity;
    }
}
