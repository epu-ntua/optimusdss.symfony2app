<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Register_Predictions
 *
 * @ORM\Table(name="RegisterPredictions")
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\RegisterPredictionsRepository")
 */
class RegisterPredictions
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;


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
     * Set date
     *
     * @param \DateTime $date
     * @return Register_Predictions
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
     * Set value
     *
     * @param float $value
     * @return Register_Predictions
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }
	
	/**
     * @ORM\ManyToOne(targetEntity="Sensor")
     * @ORM\JoinColumn(name="fk_sensor", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_sensor;
    
    
     /**
     * @ORM\ManyToOne(targetEntity="Prediction")
     * @ORM\JoinColumn(name="fk_prediction", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fk_prediction;

    /**
     * Set fk_sensor
     *
     * @param \Optimus\OptimusBundle\Entity\Sensor $fkSensor
     * @return Register_Predictions
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
     * Set fk_prediction
     *
     * @param \Optimus\OptimusBundle\Entity\Prediction $fkPrediction
     * @return Register_Predictions
     */
    public function setFkPrediction(\Optimus\OptimusBundle\Entity\Prediction $fkPrediction = null)
    {
        $this->fk_prediction = $fkPrediction;

        return $this;
    }

    /**
     * Get fk_prediction
     *
     * @return \Optimus\OptimusBundle\Entity\Prediction 
     */
    public function getFkPrediction()
    {
        return $this->fk_prediction;
    }
}
