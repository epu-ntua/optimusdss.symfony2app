<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WeeklyReport
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WeeklyReport
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
     * @ORM\Column(name="period", type="string", length=255)
     */
    private $period;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_consumption", type="float", options={"default":0.0})
     */
    private $energyConsumption;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_cost", type="float", options={"default":0.0})
     */
    private $energyCost;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_actions", type="integer", options={"default":0})
     */
    private $userActions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="monday", type="datetime")
     */
    private $monday;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", options={"default":0})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="experienceDifficulties", type="text", options={"default":" "})
     */
    private $experienceDifficulties;

    /**
     * @var string
     *
     * @ORM\Column(name="experienceCalibration", type="text", options={"default":" "})
     */
    private $experienceCalibration;

    /**
     * @var string
     *
     * @ORM\Column(name="experienceEvents", type="text", options={"default":" "})
     */
    private $experienceEvents;

    /**
	 * @ORM\ManyToOne(targetEntity="Building")
	 * @ORM\JoinColumn(name="fk_Building", referencedColumnName="id", onDelete="CASCADE")
	 */	
    private $fk_Building;


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
     * Set period
     *
     * @param string $period
     *
     * @return WeeklyReport
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set energyConsumption
     *
     * @param float $energyConsumption
     *
     * @return WeeklyReport
     */
    public function setEnergyConsumption($energyConsumption)
    {
        $this->energyConsumption = $energyConsumption;

        return $this;
    }

    /**
     * Get energyConsumption
     *
     * @return float
     */
    public function getEnergyConsumption()
    {
        return $this->energyConsumption;
    }

    /**
     * Set energyCost
     *
     * @param float $energyCost
     *
     * @return WeeklyReport
     */
    public function setEnergyCost($energyCost)
    {
        $this->energyCost = $energyCost;

        return $this;
    }

    /**
     * Get energyCost
     *
     * @return float
     */
    public function getEnergyCost()
    {
        return $this->energyCost;
    }

    /**
     * Set userActions
     *
     * @param integer $userActions
     *
     * @return WeeklyReport
     */
    public function setUserActions($userActions)
    {
        $this->userActions = $userActions;

        return $this;
    }

    /**
     * Get userActions
     *
     * @return integer
     */
    public function getUserActions()
    {
        return $this->userActions;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return WeeklyReport
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return WeeklyReport
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
     * Set experienceDifficulties
     *
     * @param string $experienceDifficulties
     *
     * @return WeeklyReport
     */
    public function setExperienceDifficulties($experienceDifficulties)
    {
        $this->experienceDifficulties = $experienceDifficulties;

        return $this;
    }

    /**
     * Get experienceDifficulties
     *
     * @return string
     */
    public function getExperienceDifficulties()
    {
        return $this->experienceDifficulties;
    }

    /**
     * Set experienceCalibration
     *
     * @param string $experienceCalibration
     *
     * @return WeeklyReport
     */
    public function setExperienceCalibration($experienceCalibration)
    {
        $this->experienceCalibration = $experienceCalibration;

        return $this;
    }

    /**
     * Get experienceCalibration
     *
     * @return string
     */
    public function getExperienceCalibration()
    {
        return $this->experienceCalibration;
    }

    /**
     * Set experienceEvents
     *
     * @param string $experienceEvents
     *
     * @return WeeklyReport
     */
    public function setExperienceEvents($experienceEvents)
    {
        $this->experienceEvents = $experienceEvents;

        return $this;
    }

    /**
     * Get experienceEvents
     *
     * @return string
     */
    public function getExperienceEvents()
    {
        return $this->experienceEvents;
    }

  

    /**
     * Set fkBuilding
     *
     * @param \Optimus\OptimusBundle\Entity\Building $fkBuilding
     *
     * @return WeeklyReport
     */
    public function setFkBuilding(\Optimus\OptimusBundle\Entity\Building $fkBuilding = null)
    {
        $this->fk_Building = $fkBuilding;

        return $this;
    }

    /**
     * Get fkBuilding
     *
     * @return \Optimus\OptimusBundle\Entity\Building
     */
    public function getFkBuilding()
    {
        return $this->fk_Building;
    }

    /**
     * Set monday
     *
     * @param \DateTime $monday
     *
     * @return WeeklyReport
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday
     *
     * @return \DateTime
     */
    public function getMonday()
    {
        return $this->monday;
    }
}
