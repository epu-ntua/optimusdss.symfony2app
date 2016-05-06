<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Events
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\EventsRepository")
 */
class Events
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
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="fk_user", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $fk_user;

    /**
     * @var string
     *
     * @ORM\Column(name="textEvent", type="string", length=255)
     */
    private $textEvent;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=255)
     */
    private $context;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_context", type="integer", nullable=true)
     */
    private $id_context;
	
	
	/**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible;
	
	/** 
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="fk_Building", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_Building;
	
	/**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    protected $action;

    
	
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
     * @return Events
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
     * Set fkUser
     *
     * @param integer $fkUser
     * @return Events
     */
    public function setFkUser(\Optimus\OptimusBundle\Entity\Users $fkUser = null)
    {
        $this->fk_user = $fkUser;

        return $this;
    }

    /**
     * Get fkUser
     *
     * @return integer 
     */
    public function getFkUser()
    {
        return $this->fk_user;
    }
   

    /**
     * Set context
     *
     * @param string $context
     * @return Events
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string 
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Events
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Events
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set fk_Building
     *
     * @param \Optimus\OptimusBundle\Entity\Building $fkBuilding
     * @return Events
     */
    public function setFkBuilding(\Optimus\OptimusBundle\Entity\Building $fkBuilding = null)
    {
        $this->fk_Building = $fkBuilding;

        return $this;
    }

    /**
     * Get fk_Building
     *
     * @return \Optimus\OptimusBundle\Entity\Building 
     */
    public function getFkBuilding()
    {
        return $this->fk_Building;
    }

    /**
     * Set textEvent
     *
     * @param string $textEvent
     * @return Events
     */
    public function setTextEvent($textEvent)
    {
        $this->textEvent = $textEvent;

        return $this;
    }

    /**
     * Get textEvent
     *
     * @return string 
     */
    public function getTextEvent()
    {
        return $this->textEvent;
    }

    /**
     * Set id_context
     *
     * @param integer $idContext
     * @return Events
     */
    public function setIdContext($idContext)
    {
        $this->id_context = $idContext;

        return $this;
    }

    /**
     * Get id_context
     *
     * @return integer 
     */
    public function getIdContext()
    {
        return $this->id_context;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Events
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }
}
