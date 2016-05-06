<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 20/7/2015
 * Time: 4:51 μμ
 */

namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Optimus\OptimusBundle\Servicios\ServiceAPTCV;

class TCVController extends Controller
{
    public function plan_indexAction($idBuilding, $start_date)
    {
        $service = new ServiceAPTCV($this->getDoctrine()->getEntityManager());

        //find latest week start
        $prev_week_start = date('Y-m-d', strtotime('last Monday', strtotime($start_date)));

        // invoke service based on last week data
        $result = $service->invoke_service($idBuilding, $prev_week_start);

        /* Set date */
        $exp = explode("-", $start_date);
        $result['start_date']['year'] = $exp[0];
        $result['start_date']['month'] = $exp[1];
        $result['start_date']['day'] = $exp[2];

        // render the template
        return $this->render('OptimusOptimusBundle:PVActionPlan:tcv-details.html.twig', $result);
    }
}