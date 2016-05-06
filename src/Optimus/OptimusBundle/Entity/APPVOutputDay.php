<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APPVOutputDay
 *
 * @ORM\Table(name="APPVOutputDay")
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APPVOutputDayRepository")
 */
class APPVOutputDay
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
     * @var string
     *
     * @ORM\Column(name="strategy", type="string", length=255)
     */
    private $strategy;


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
     * @return APPVOutputDay
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
     * @return APPVOutputDay
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
     * @return APPVOutputDay
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
     * Set strategy
     *
     * @param string $strategy
     * @return APPVOutputDay
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Get strategy
     *
     * @return string 
     */
    public function getStrategy()
    {
        return $this->strategy;
    }
}
