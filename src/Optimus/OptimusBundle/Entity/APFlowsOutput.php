<?php

namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * APFlowsOutput
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\APFlowsOutputRepository")
 */
class APFlowsOutput
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
     * @ORM\Column(name="hour_timestamp", type="datetime")
     */
    private $hour_timestamp;
  
	/**
     * @var float
     *
     * @ORM\Column(name="load_value", type="float")
     */
	private $load_value;
	
	/**
     * @var float
     *
     * @ORM\Column(name="grid", type="float")
     */
	private $grid;
	
	/**
     * @var float
     *
     * @ORM\Column(name="res", type="float")
     */
	private $res;
	
	/**
     * @var float
     *
     * @ORM\Column(name="shaving", type="float")
     */
	private $shaving;
	
	/**
     * @var float
     *
     * @ORM\Column(name="load_original", type="float")
     */
	private $load_original;
	
	/**
     * @var float
     *
     * @ORM\Column(name="grid_original", type="float")
     */
	private $grid_original;
	
	/**
     * @var float
     *
     * @ORM\Column(name="storage", type="float")
     */
	private $storage;
	
	/**
     * @var float
     *
     * @ORM\Column(name="ThA", type="float")
     */
	private $ThA;
	
	/**
     * @var float
     *
     * @ORM\Column(name="ThB", type="float")
     */
	private $ThB;
	
	/**
     * @var int
     *
     * @ORM\Column(name="Aon", type="integer")
     */
	private $Aon;
	
		/**
     * @var int
     *
     * @ORM\Column(name="Bon", type="integer")
     */
	private $Bon;
    
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
     * Set fkApCalculation
     *
     * @param \Optimus\OptimusBundle\Entity\APCalculation $fkApCalculation
     * @return AP_Flows_Output
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
     * Set hour_timestamp
     *
     * @param \DateTime $hour_timestamp
     * @return AP_Flows_Output
     */
    public function setHour_timestamp($hour_timestamp)
    {
        $this->hour_timestamp = $hour_timestamp;

        return $this;
    }

    /**
     * Get hour_timestamp
     *
     * @return \DateTime 
     */
    public function getHour_timestamp()
    {
        return $this->hour_timestamp;
    }

    /**
     * Set load_value
     *
     * @param float $load_value
     * @return APFlowsOutput
     */
    public function setLoad_value($load_value)
    {
        $this->load_value = $load_value;

        return $this;
    }

    /**
     * Get load_value
     *
     * @return float 
     */
    public function getLoad_value()
    {
        return $this->load_value;
    }

	/**
     * Set grid
     *
     * @param float $grid
     * @return APFlowsOutput
     */
    public function setGrid($grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * Get grid
     *
     * @return grid 
     */
    public function getGrid()
    {
        return $this->grid;
    }
	
	/**
     * Set res
     *
     * @param float $res
     * @return APFlowsOutput
     */
    public function setRes($res)
    {
        $this->res = $res;

        return $this;
    }

    /**
     * Get res
     *
     * @return res 
     */
    public function getRes()
    {
        return $this->res;
    }
	
	/**
     * Set shaving
     *
     * @param float $shaving
     * @return APFlowsOutput
     */
    public function setShaving($shaving)
    {
        $this->shaving = $shaving;

        return $this;
    }

    /**
     * Get shaving
     *
     * @return shaving 
     */
    public function getShaving()
    {
        return $this->shaving;
    }
	
	/**
     * Set load_original
     *
     * @param float $load_original
     * @return APFlowsOutput
     */
    public function setLoad_original($load_original)
    {
        $this->load_original = $load_original;

        return $this;
    }

    /**
     * Get load_original
     *
     * @return float 
     */
    public function getLoad_original()
    {
        return $this->load_original;
    }

	/**
     * Set grid_original
     *
     * @param float $grid_original
     * @return APFlowsOutput
     */
    public function setGrid_original($grid_original)
    {
        $this->grid_original = $grid_original;

        return $this;
    }

    /**
     * Get grid_original
     *
     * @return grid_original 
     */
    public function getGrid_original()
    {
        return $this->grid_original;
    }
	
	/**
     * Set storage
     *
     * @param float $storage
     * @return APFlowsOutput
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get storage
     *
     * @return storage 
     */
    public function getStorage()
    {
        return $this->storage;
    }
	
	/**
     * Set ThA
     *
     * @param float $ThA
     * @return APFlowsOutput
     */
    public function setThA($ThA)
    {
        $this->ThA = $ThA;

        return $this;
    }

    /**
     * Get ThA
     *
     * @return ThA 
     */
    public function getThA()
    {
        return $this->ThA;
    }
	
	/**
     * Set ThB
     *
     * @param float $ThB
     * @return APFlowsOutput
     */
    public function setThB($ThB)
    {
        $this->ThB = $ThB;

        return $this;
    }
	
	 /**
     * Get ThB
     *
     * @return ThB 
     */
    public function getThB()
    {
        return $this->ThB;
    }
	
    /**
     * Get Aon
     *
     * @return Aon 
     */
    public function getAon()
    {
        return $this->Aon;
    }
	
	/**
     * Set Aon
     *
     * @param float $Aon
     * @return APFlowsOutput
     */
    public function setAon($Aon)
    {
        $this->Aon = $Aon;

        return $this;
    }
	
    /**
     * Get Bon
     *
     * @return Bon 
     */
    public function getBon()
    {
        return $this->Bon;
    }
	
	/**
     * Set Bon
     *
     * @param float $Bon
     * @return APFlowsOutput
     */
    public function setBon($Bon)
    {
        $this->Bon = $Bon;

        return $this;
    }
    
}
