<?php
namespace Optimus\OptimusBundle\Servicios\Util\TCV;

class ThermalSensation {
    protected $sensation; // integer in the [-3,3] range
    protected $scale = ["Cold" => -3, "Cool" => -2, "Slightly Cool" => -1, "Neutral" => 0, "Slightly Warm" => 1, "Warm" => 2, "Hot" => 3];

    /*
     * Sets the numerical value from the linguistic term
     */
    public function from_string($sensation_str)
    {
        $this->sensation = $this->scale[$sensation_str];
        return $this;
    }

    /*
     * Sets the numerical value from a decimal approximation
     */
    public function from_decimal($appr) {
        $this->sensation = round($appr);
        return $this;
    }

    /*
     * Returns the sensation value
     */
    public function get_sensation() {
        return $this->sensation;
    }

    /*
     * Returns the sensation value
     */
    public function get_sensation_term() {
        return array_search($this->sensation, $this->scale);
    }
}