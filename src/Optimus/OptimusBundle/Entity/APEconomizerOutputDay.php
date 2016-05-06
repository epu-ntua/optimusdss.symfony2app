<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APEconomizerOutputDay
 *
 * @ORM\Table(name="APEconOutputDay")
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APEconomizerOutputDayRepository")
 */
class APEconomizerOutputDay
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
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
	
	/**
     * @var float
     *
     * @ORM\Column(name="temp_internal", type="float")
     */
    private $temp_internal;
	
	/**
     * @var float
     *
     * @ORM\Column(name="enth_internal", type="float")
     */
    private $enth_internal;

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
     * @param string $fkApCalculation
     * @return APPVMOutputDay
     */
    public function setFkApCalculation(\Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation)
    {
        $this->fkApCalculation = $fkApCalculation;

        return $this;
    }

    /**
     * Get fkApCalculation
     *
     * @return string 
     */
    public function getFkApCalculation()
    {
        return $this->fkApCalculation;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return APPVMOutputDay
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
     * Set status
     *
     * @param integer $status
     * @return APPVMOutputDay
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
	
	/**
     * Set temp_internal
     *
     * @param float $temp_internal
     * @return APEconomizerOutputDay
     */
    public function setTemp_internal($temp_internal)
    {
        $this->temp_internal = $temp_internal;

        return $this;
    }

    /**
     * Get temp_internal
     *
     * @return float 
     */
    public function getTemp_internal()
    {
        return $this->temp_internal;
    }
	
	/**
     * Set enth_internal
     *
     * @param float $enth_internal
     * @return APEconomizerOutputDay
     */
    public function setEnth_internal($enth_internal)
    {
        $this->enth_internal = $enth_internal;

        return $this;
    }

    /**
     * Get enth_internal
     *
     * @return float 
     */
    public function getEnth_internal()
    {
        return $this->enth_internal;
    }
}
