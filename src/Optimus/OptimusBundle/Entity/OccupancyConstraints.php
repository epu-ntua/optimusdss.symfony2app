<?php
namespace Optimus\OptimusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OccupancyConstraints
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Optimus\OptimusBundle\Repository\OccupancyConstraintsRepository")
 */

class OccupancyConstraints
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
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=255)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(name="my_constraint", type="string", length=255)
     */
    private $my_constraint;

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
     * @return OccupancyConstraints
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
     * Set my_constraint
     *
     * @param string $my_constraint
     * @return OccupancyConstraints
     */
    public function setMyConstraint($my_constraint)
    {
        $this->my_constraint = $my_constraint;

        return $this;
    }

    /**
     * Get my_constraint
     *
     * @return string
     */
    public function getMyConstraint()
    {
        return $this->my_constraint;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return OccupancyConstraints
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
}