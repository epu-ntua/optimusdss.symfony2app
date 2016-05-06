<?php
namespace Optimus\OptimusBundle\Servicios\Util\TCV;

/* The PMVCalculator transfers VBScript to PHP
 * The class enables the calculation of the PMV function based on some initial conditions
 *
 */
class PMVCalculator {
    private function fnps($t)
    {
        return exp(16.6536 - 4030.183 / ($t + 235));
    }

    public function pmv($ta, $tr, $vel, $rh, $clo, $met, $ew)
    {
        /* A check about RH or PA could be added at this point */
        /* Logic copied from vb script */
        /* Variable names too... */
        $pa = $rh * 10 * $this->fnps($ta);
        $mr = $met * 58.15;
        $mw = $mr - $ew * 58.15;
        $icl = 0.155 * $clo;
        If ($icl <= 0.078) {
            $fcl = 1 + 1.29 * $icl;
        } else {
            $fcl = 1.05 + 0.645 * $icl;
        }

        $hcf = 12.1 * sqrt($vel);
        $taa = $ta + 273;
        $tra = $tr + 273;

        $tcla = $taa + (35.5 - $ta) / (3.5 * (6.45 * $icl + 0.1));
        $p1 = $icl * $fcl;
        $p2 = $p1 * 3.96;
        $p3 = $p1 * 100;
        $p4 = $p1 * $taa;
        $p5 = 308.7 - 0.028 * $mw + $p2 * pow($tra / 100, 4); // original: P2 * (TRA / 100) ^ 4
        $xn = $tcla / 100;
        $xf = $xn;
        $n = 0;
        $epsilon = 0.00015;

        do {
            $xf = ($xn + $xf) / 2;
            $hcn = 2.38 * pow(abs(100 * $xf - $taa), 0.25);
            if ($hcf > $hcn) {
                $hc = $hcf;
            } else {
                $hc = $hcn;
            }

            $xn = ($p5 + $p4 * $hc - $p2 * pow($xf, 4)) / (100 + $p3 * $hc);
            $n++;
        }
        while (((abs($xn - $xf) >= $epsilon) && ($n <= 150)));

        if ($n < 150) {
            $tcl = 100 * $xn - 273;
            $hl1 = 3.05 * 0.001 * (5733 - 6.99 * $mw - $pa);
            if ($mw > 58.15) {
                $hl2 = 0.42 * ($mw - 58.15);
            } else {
                $hl2 = 0;
            }
            $hl3 = 1.7 * 0.00001 * $mr * (5867 - $pa);
            $hl4 = 0.0014 * $mr * (34 - $ta);
            $hl5 = 3.96 * $fcl * (pow($xn, 4) - pow(($tra / 100), 4));
            $hl6 = $fcl * $hc * ($tcl - $ta);
            $ts = 0.303 * exp(-0.036 * $mr) + 0.028;
            $pmv = $ts * ($mw - $hl1 - $hl2 - $hl3 - $hl4 - $hl5 - $hl6);
        } else {
            $pmv = 999999;
        }

        return $pmv;
    }

    public function ppd($pmv)
    {
        if ($pmv == 999999) {
            $ppd = 100;
        } else {
            $ppd = 100 - 95 * exp(-0.03353 * pow($pmv, 4) - 0.2179 * pow($pmv, 2));
        }

        return $ppd;
    }



}