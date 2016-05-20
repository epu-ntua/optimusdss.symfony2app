<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APFlowsOutputDay
 *
 * @ORM\Table(name="APFlowsOutputDay")
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APFlowsOutputDayRepository")
 */
class APFlowsOutputDay
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
     * @return APFlowsOutputDay
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
     * @return APFlowsOutputDay
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
     * @return APFlowsOutputDay
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
}
