<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WeeklyReportUsers
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WeeklyReportUsers
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
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $fk_user;


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
     * Set fkWeeklyreport
     *
     * @param \Optimus\OptimusBundle\Entity\WeeklyReport $fkWeeklyreport
     *
     * @return WeeklyReportUsers
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
     * Set fkUser
     *
     * @param \Optimus\OptimusBundle\Entity\Users $fkUser
     *
     * @return WeeklyReportUsers
     */
    public function setFkUser(\Optimus\OptimusBundle\Entity\Users $fkUser = null)
    {
        $this->fk_user = $fkUser;

        return $this;
    }

    /**
     * Get fkUser
     *
     * @return \Optimus\OptimusBundle\Entity\Users
     */
    public function getFkUser()
    {
        return $this->fk_user;
    }
}
