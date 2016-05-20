<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WeeklyReportActionPlan
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WeeklyReportActionPlan
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
	 * @ORM\ManyToOne(targetEntity="WeeklyReport")
	 * @ORM\JoinColumn(name="fk_weeklyreport", referencedColumnName="id", onDelete="CASCADE")
	 */
    private $fk_weeklyreport;

    /**
	 * @ORM\ManyToOne(targetEntity="ActionPlans")
	 * @ORM\JoinColumn(name="fk_actionplan", referencedColumnName="id", onDelete="CASCADE")
	 */
    protected $fk_actionplan;

   

    /**
     * @var string
     *
     * @ORM\Column(name="textProcedure", type="text", options={"default":""})
     */
    private $textProcedure;

    /**
     * @var string
     *
     * @ORM\Column(name="lessonLearned", type="text", options={"default":""})
     */
    private $lessonLearned;


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
     * Set textProcedure
     *
     * @param string $textProcedure
     *
     * @return WeeklyReportActionPlan
     */
    public function setTextProcedure($textProcedure)
    {
        $this->textProcedure = $textProcedure;

        return $this;
    }

    /**
     * Get textProcedure
     *
     * @return string
     */
    public function getTextProcedure()
    {
        return $this->textProcedure;
    }

    /**
     * Set lessonLearned
     *
     * @param string $lessonLearned
     *
     * @return WeeklyReportActionPlan
     */
    public function setLessonLearned($lessonLearned)
    {
        $this->lessonLearned = $lessonLearned;

        return $this;
    }

    /**
     * Get lessonLearned
     *
     * @return string
     */
    public function getLessonLearned()
    {
        return $this->lessonLearned;
    }

    /**
     * Set fkWeeklyreport
     *
     * @param \Optimus\OptimusBundle\Entity\WeeklyReport $fkWeeklyreport
     *
     * @return WeeklyReportActionPlan
     */
    public function setFkWeeklyreport(\Optimus\OptimusBundle\Entity\WeeklyReport $fkWeeklyreport = null)
    {
        $this->fk_weeklyreport = $fkWeeklyreport;

        return $this;
    }

    /**
     * Get fkWeeklyreport
     *
     * @return \Optimus\OptimusBundle\Entity\WeeklyReport
     */
    public function getFkWeeklyreport()
    {
        return $this->fk_weeklyreport;
    }

    /**
     * Set fkActionplan
     *
     * @param \Optimus\OptimusBundle\Entity\ActionPlans $fkActionplan
     *
     * @return WeeklyReportActionPlan
     */
    public function setFkActionplan(\Optimus\OptimusBundle\Entity\ActionPlans $fkActionplan = null)
    {
        $this->fk_actionplan = $fkActionplan;

        return $this;
    }

    /**
     * Get fkActionplan
     *
     * @return \Optimus\OptimusBundle\Entity\ActionPlans
     */
    public function getFkActionplan()
    {
        return $this->fk_actionplan;
    }
}
