<?php
namespace Optimus\OptimusBundle\Twig;

use Optimus\OptimusBundle\Servicios\Util\TCV\ThermalSensation;

class TCVExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sensation_term', array($this, 'sensation_term')),
            new \Twig_SimpleFilter('sensation_value', array($this, 'sensation_value')),
            new \Twig_SimpleFilter('sensation_class', array($this, 'sensation_class')),
            new \Twig_SimpleFilter('from_tcv_date', array($this, 'from_tcv_date'))
        );
    }

    /*
     * $record is an array with 'value' and 'size' keys
     * Gets the most approximate linguistic term
     */
    public function sensation_term($record)
    {
        $sensation = new ThermalSensation();
        return $sensation->from_decimal($record['value']/$record['size'])->get_sensation_term();
    }

    /*
     * $record is an array with 'value' and 'size' keys
     * Gets the most approximate numerical value
     */
    public function sensation_value($record)
    {
        $sensation = new ThermalSensation();
        return $sensation->from_decimal($record['value']/$record['size'])->get_sensation();
    }

    /*
     * Get class name for the $record sensation term
     */
    public function sensation_class($record)
    {
        $sensation = new ThermalSensation();
        return "tcv-sensation-" . str_replace("-", "minus", $sensation->from_decimal($record['value']/$record['size'])->get_sensation());
    }

    /*
     * Turns TCV Service date format to something more human readable
     * e.g 20150209 turns to 02/09
     */
    public function from_tcv_date($tcv_date)
    {
        return $tcv_date[6] . $tcv_date[7] . "/" . $tcv_date[4] . $tcv_date[5];
    }

    public function getName()
    {
        return 'tcv_extension';
    }
}