<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvaluationCriteria
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EvaluationCriteria
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
     * @var integer
     *
     * @ORM\Column(name="score1", type="integer", options={"default":1})
     */
    private $score1;

    /**
     * @var string
     *
     * @ORM\Column(name="text1", type="text", options={"default":""})
     */
    private $text1;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="score2", type="integer", options={"default":1})
     */
    private $score2;

    /**
     * @var string
     *
     * @ORM\Column(name="text2", type="text", options={"default":""})
     */
    private $text2;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="score3", type="integer", options={"default":1})
     */
    private $score3;

    /**
     * @var string
     *
     * @ORM\Column(name="text3", type="text", options={"default":""})
     */
    private $text3;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="score4", type="integer", options={"default":1})
     */
    private $score4;

    /**
     * @var string
     *
     * @ORM\Column(name="text4", type="text", options={"default":""})
     */
    private $text4;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="score5", type="integer", options={"default":1})
     */
    private $score5;

    /**
     * @var string
     *
     * @ORM\Column(name="text5", type="text", options={"default":""})
     */
    private $text5;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="score6", type="integer", options={"default":1})
     */
    private $score6;

    /**
     * @var string
     *
     * @ORM\Column(name="text6", type="text", options={"default":""})
     */
    private $text6;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="score7", type="integer", options={"default":1})
     */
    private $score7;

    /**
     * @var string
     *
     * @ORM\Column(name="text7", type="text", options={"default":""})
     */
    private $text7;


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
     * Set score1
     *
     * @param integer $score1
     *
     * @return EvaluationCriteria
     */
    public function setScore1($score1)
    {
        $this->score1 = $score1;

        return $this;
    }

    /**
     * Get score1
     *
     * @return integer
     */
    public function getScore1()
    {
        return $this->score1;
    }

    /**
     * Set text1
     *
     * @param string $text1
     *
     * @return EvaluationCriteria
     */
    public function setText1($text1)
    {
        $this->text1 = $text1;

        return $this;
    }

    /**
     * Get text1
     *
     * @return string
     */
    public function getText1()
    {
        return $this->text1;
    }

    /**
     * Set score2
     *
     * @param integer $score2
     *
     * @return EvaluationCriteria
     */
    public function setScore2($score2)
    {
        $this->score2 = $score2;

        return $this;
    }

    /**
     * Get score2
     *
     * @return integer
     */
    public function getScore2()
    {
        return $this->score2;
    }

    /**
     * Set text2
     *
     * @param string $text2
     *
     * @return EvaluationCriteria
     */
    public function setText2($text2)
    {
        $this->text2 = $text2;

        return $this;
    }

    /**
     * Get text2
     *
     * @return string
     */
    public function getText2()
    {
        return $this->text2;
    }

    /**
     * Set score3
     *
     * @param integer $score3
     *
     * @return EvaluationCriteria
     */
    public function setScore3($score3)
    {
        $this->score3 = $score3;

        return $this;
    }

    /**
     * Get score3
     *
     * @return integer
     */
    public function getScore3()
    {
        return $this->score3;
    }

    /**
     * Set text3
     *
     * @param string $text3
     *
     * @return EvaluationCriteria
     */
    public function setText3($text3)
    {
        $this->text3 = $text3;

        return $this;
    }

    /**
     * Get text3
     *
     * @return string
     */
    public function getText3()
    {
        return $this->text3;
    }

    /**
     * Set score4
     *
     * @param integer $score4
     *
     * @return EvaluationCriteria
     */
    public function setScore4($score4)
    {
        $this->score4 = $score4;

        return $this;
    }

    /**
     * Get score4
     *
     * @return integer
     */
    public function getScore4()
    {
        return $this->score4;
    }

    /**
     * Set text4
     *
     * @param string $text4
     *
     * @return EvaluationCriteria
     */
    public function setText4($text4)
    {
        $this->text4 = $text4;

        return $this;
    }

    /**
     * Get text4
     *
     * @return string
     */
    public function getText4()
    {
        return $this->text4;
    }

    /**
     * Set score5
     *
     * @param integer $score5
     *
     * @return EvaluationCriteria
     */
    public function setScore5($score5)
    {
        $this->score5 = $score5;

        return $this;
    }

    /**
     * Get score5
     *
     * @return integer
     */
    public function getScore5()
    {
        return $this->score5;
    }

    /**
     * Set text5
     *
     * @param string $text5
     *
     * @return EvaluationCriteria
     */
    public function setText5($text5)
    {
        $this->text5 = $text5;

        return $this;
    }

    /**
     * Get text5
     *
     * @return string
     */
    public function getText5()
    {
        return $this->text5;
    }

    /**
     * Set score6
     *
     * @param integer $score6
     *
     * @return EvaluationCriteria
     */
    public function setScore6($score6)
    {
        $this->score6 = $score6;

        return $this;
    }

    /**
     * Get score6
     *
     * @return integer
     */
    public function getScore6()
    {
        return $this->score6;
    }

    /**
     * Set text6
     *
     * @param string $text6
     *
     * @return EvaluationCriteria
     */
    public function setText6($text6)
    {
        $this->text6 = $text6;

        return $this;
    }

    /**
     * Get text6
     *
     * @return string
     */
    public function getText6()
    {
        return $this->text6;
    }

    /**
     * Set score7
     *
     * @param integer $score7
     *
     * @return EvaluationCriteria
     */
    public function setScore7($score7)
    {
        $this->score7 = $score7;

        return $this;
    }

    /**
     * Get score7
     *
     * @return integer
     */
    public function getScore7()
    {
        return $this->score7;
    }

    /**
     * Set text7
     *
     * @param string $text7
     *
     * @return EvaluationCriteria
     */
    public function setText7($text7)
    {
        $this->text7 = $text7;

        return $this;
    }

    /**
     * Get text7
     *
     * @return string
     */
    public function getText7()
    {
        return $this->text7;
    }

    /**
     * Set fkWeeklyreport
     *
     * @param \Optimus\OptimusBundle\Entity\WeeklyReport $fkWeeklyreport
     *
     * @return EvaluationCriteria
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
    
}
