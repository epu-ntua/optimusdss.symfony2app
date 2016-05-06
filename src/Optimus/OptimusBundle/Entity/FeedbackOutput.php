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
	 * @ORM\ManyToOne(targetEntity="APCalculation")
	 * @ORM\JoinColumn(name="fk_ap_calculation", referencedColumnName="id", onDelete="CASCADE")
	 */
    protected $fkApCalculation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="full_date", type="datetime")
     */
    private $full_date;

    /**
     * @var integer
     *
     * @ORM\Column(name="fk_Proposition_Id", type="integer")
     */
    protected $fkPropositionId;

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
     * Set fkPropositionId
     *
     * @param integer $fkPropositionId
     * @return Feedback_Output
     */
    public function setFkPropositionId($fkPropositionId)
    {
        $this->fkPropositionId = $fkPropositionId;

        return $this;
    }

    /**
     * Get fkPropositionId
     *
     * @return integer
     */
    public function getFkPropositionId()
    {
        return $this->fkPropositionId;
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

}
