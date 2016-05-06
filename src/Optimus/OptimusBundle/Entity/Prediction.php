<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prediction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\PredictionRepository")
 */
class Prediction
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="datetime")
     */
    private $dateCreate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_user", type="datetime")
     */
    private $dateUser;
	
	/**
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="fk_Building", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_Building;

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
     * Set name
     *
     * @param string $name
     * @return Prediction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Prediction
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUser
     *
     * @param \DateTime $dateUser
     * @return Prediction
     */
    public function setDateUser($dateUser)
    {
        $this->dateUser = $dateUser;

        return $this;
    }

    /**
     * Get dateUser
     *
     * @return \DateTime 
     */
    public function getDateUser()
    {
        return $this->dateUser;
    }
	
	

    /**
     * Set fk_Building
     *
     * @param \Optimus\OptimusBundle\Entity\Building $fkBuilding
     * @return Prediction
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
}
