<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AP_Calculation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APCalculationRepository")
 */
class APCalculation
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
     * @var \DateTime
     *
     * @ORM\Column(name="starting_date", type="datetime")
     */
    private $startingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;


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
     * Set startingDate
     *
     * @param \DateTime $startingDate
     * @return APCalculation
     */
    public function setStartingDate($startingDate)
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    /**
     * Get startingDate
     *
     * @return \DateTime 
     */
    public function getStartingDate()
    {
        return $this->startingDate;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return APCalculation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set fk_actionplan
     *
     * @param \Optimus\OptimusBundle\Entity\ActionPlans $fkActionplan
     * @return AP_Calculation
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
}
