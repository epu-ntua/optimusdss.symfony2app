<?php
/**
 * Created by PhpStorm.
 * User: CHRISTINE
 * Date: 31-Dec-15
 * Time: 8:10 PM
 */

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AP_Adaptive_Output
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APAdaptiveOutputRepository")
 */

class APAdaptiveOutput
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="daily_mean", type="float")
     */
    private $dailyMean;

    /**
     * @var float
     *
     * @ORM\Column(name="set_point", type="float")
     */
    private $setPoint;


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
     * @return AP_Adaptive_Output
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
     * Set dailyMean
     *
     * @param float $dailyMean
     * @return AP_Adaptive_Output
     */
    public function setDailyMean($dailyMean)
    {
        $this->dailyMean = $dailyMean;

        return $this;
    }

    /**
     * Get dailyMean
     *
     * @return float
     */
    public function getDailyMean()
    {
        return $this->dailyMean;
    }

    /**
     * Set setPoint
     *
     * @param float $setPoint
     * @return AP_Adaptive_Output
     */
    public function setSetPoint($setPoint)
    {
        $this->setPoint = $setPoint;

        return $this;
    }

    /**
     * Get setPoint
     *
     * @return float
     */
    public function getSetPoint()
    {
        return $this->setPoint;
    }

    /**
     * Set fkApCalculation
     *
     * @param \Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation
     * @return AP_Adaptive_Output
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
