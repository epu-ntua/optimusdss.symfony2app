<?php
namespace Optimus\OptimusBundle\Servicios\Util\TCV;

/* The LinearParameters class accepts a set of points as its input
 * The $points variable is an array containing associative arrays with 'x' and 'y' keys set to numerical values
 * As a result, the $slope and $intercept parameters of the object are set
 */
class LinearParameters {
    protected $points;
    protected $slope;
    protected $intercept;

    public function __construct($points) {
        $this->set_points($points);
    }

    public function get_points() {
        return $this->points;
    }

    public function set_points($points) {
        // set the new $points array
        $this->points = $points;

        //re-calculate $slope and $intercept
        $x_avg = 0;
        $y_avg = 0;
        foreach($points as $point) {
            $x_avg += $point['x'];
            $y_avg += $point['y'];
        }
        $x_avg /= count($points);
        $y_avg /= count($points);

        // see https://support.office.com/en-ca/article/INTERCEPT-function-2a9b74e2-9d47-4772-b663-3bca70bf63ef for the algorithm
        // calculate slope
        $devidend = 0;
        $devidor = 0;
        foreach($points as $point) {
            $x_diff = $point['x'] - $x_avg;
            $devidend += $x_diff*($point['y'] - $y_avg);
            $devidor += pow($x_diff, 2);
        }

        $this->slope = $devidend/$devidor;

        // calculate intercept
        $this->intercept = $y_avg - $this->slope*$x_avg;
    }

    public function get_slope() {
        return $this->slope;
    }

    public function get_intercept() {
        return $this->intercept;
    }
}