<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingPartitioning
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\BuildingPartitioningRepository")
 */
class BuildingPartitioning
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
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="fk_Building", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fk_Building;

    /**
     * @var string
     *
     * @ORM\Column(name="partition_name", type="string", length=255)
     */
    private $partitionName;
	
	/**
	 * @ORM\OneToMany(targetEntity="BuildingPartitioning", mappedBy="fk_BuildingPartitioning", orphanRemoval=true, cascade={"persist", "remove"})
	 */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="BuildingPartitioning", inversedBy="children")
     * @ORM\JoinColumn(name="fk_BuildingPartitioning", referencedColumnName="id")
     */
    protected $fk_BuildingPartitioning;

    /**
     * @var float
     *
     * @ORM\Column(name="energy_consumption", type="float")
     */
    protected $energy_consumption;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_capacity", type="integer")
     */
    protected $max_capacity;

	
	public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set fkBuilding
     *
     * @param \Optimus\OptimusBundle\Entity\Building $fkBuilding
     * @return BuildingPartitioning
     */
    public function setFkBuilding(\Optimus\OptimusBundle\Entity\Building $fkBuilding = null)
    {
        $this->fk_Building = $fkBuilding;

        return $this;
    }

    /**
     * Get fkBuilding
     *
     * @return \Optimus\OptimusBundle\Entity\Building $fkBuilding 
     */
    public function getFkBuilding()
    {
        return $this->fk_Building;
    }

    /**
     * Set partitionName
     *
     * @param string $partitionName
     * @return BuildingPartitioning
     */
    public function setPartitionName($partitionName)
    {
        $this->partitionName = $partitionName;

        return $this;
    }

    /**
     * Get partitionName
     *
     * @return string 
     */
    public function getPartitionName()
    {
        return $this->partitionName;
    }

    /**
     * Set fkBuildingPartitioning
     *
     * @param Optimus\OptimusBundle\Entity\BuildingPartitioning $fkBuildingPartitioning
     * @return BuildingPartitioning
     */
    public function setFkBuildingPartitioning(\Optimus\OptimusBundle\Entity\BuildingPartitioning $fkBuildingPartitioning)
    {
        $this->fk_BuildingPartitioning = $fkBuildingPartitioning;

        return $this;
    }

    /**
     * Get fkBuildingPartitioning
     *
     * @return Optimus\OptimusBundle\Entity\BuildingPartitioning 
     */
    public function getFkBuildingPartitioning()
    {
        return $this->fk_BuildingPartitioning;
    }

    /**
     * Add children
     *
     * @param \Optimus\OptimusBundle\Entity\BuildingPartitioning $children
     * @return BuildingPartitioning
     */
    public function addChild(\Optimus\OptimusBundle\Entity\BuildingPartitioning $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Optimus\OptimusBundle\Entity\BuildingPartitioning $children
     */
    public function removeChild(\Optimus\OptimusBundle\Entity\BuildingPartitioning $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get EnergyConsumption
     *
     * @return float
     */
    public function getEnergyConsumption() {
        return $this->energy_consumption;
    }

    /**
     * Set EnergyConsumption
     *
     * @param float $energy_consumption
     * @return BuildingPartitioning
     */
    public function setEnergyConsumption($energy_consumption) {
        $this->energy_consumption = $energy_consumption;
        return $this;
    }

    /**
     * Get MaxCapacity
     *
     * @return integer
     */
    public function getMaxCapacity() {
        return $this->max_capacity;
    }

    /**
     * Set MaxCapacity
     *
     * @param integer $max_capacity
     * @return BuildingPartitioning
     */
    public function setMaxCapacity($max_capacity) {
        $this->max_capacity = $max_capacity;
        return $this;
    }
}
