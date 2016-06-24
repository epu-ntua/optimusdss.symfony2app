<?php
namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback_Output
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\FeedbackOutputRepository")
 */

class FeedbackOutput
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
     * @ORM\Column(name="full_date", type="datetime")
     */
    private $full_date;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=255)
     */
    protected $section;
	
	/**
	 * @ORM\ManyToOne(targetEntity="APCalculation")
	 * @ORM\JoinColumn(name="fk_ap_calculation", referencedColumnName="id", onDelete="CASCADE")
	 */
    protected $fkApCalculation;

    /**
     * @var float
     *
     * @ORM\Column(name="feedback", type="float")
     */
    private $feedback;

    /**
     * @var integer
     *
     * @ORM\Column(name="feedback_size", type="integer")
     */
    private $feedbackSize;

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
     * Set full_date
     *
     * @param \DateTime $full_date
     * @return Feedback_Output
     */
    public function setFullDate($full_date)
    {
        $this->full_date = $full_date;

        return $this;
    }

    /**
     * Get full_date
     *
     * @return \DateTime
     */
    public function getFullDate()
    {
        return $this->full_date;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return Feedback_Output
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }
	
	/**
     * Set fkApCalculation
     *
     * @param \Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation
     * @return AP_PV_Output
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
     * Set feedback
     *
     * @param float $feedback
     * @return Feedback_Output
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback
     *
     * @return float
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set feedbackSize
     *
     * @param integer $feedbackSize
     * @return Feedback_Output
     */
    public function setFeedbackSize($feedbackSize)
    {
        $this->feedbackSize = $feedbackSize;

        return $this;
    }

    /**
     * Get feedbackSize
     *
     * @return integer
     */
    public function getFeedbackSize()
    {
        return $this->feedbackSize;
    }

}
