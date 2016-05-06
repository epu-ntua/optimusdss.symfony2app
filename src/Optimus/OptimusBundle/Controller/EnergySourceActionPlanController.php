<?php


namespace Optimus\OptimusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Optimus\OptimusBundle\Servicios\ServiceAPSource;

class EnergySourceActionPlanController extends Controller
{
    public function plan_indexAction($idBuilding, $idAPType='', $from='', $to='')
    {
		if($from=='')
		{
			$dateActual=new \DateTime();			
			$startDate=\DateTime::createFromFormat('Y-m-d H:i:s', $dateActual->format("Y-m-d H:i:s"));				
			$startDateFunction=$startDate->format("Y-m-d H:i:s");
			$data['startDate']=$startDate->format("Y-m-d");
			$data['endDate']=\DateTime::createFromFormat('Y-m-d', $startDate->format("Y-m-d"))->modify("+6 day")->format("Y-m-d");
		}else{
			$startDateFunction=$from." 00:00:00";
			$data['startDate']=$from;
			$data['endDate']=$to;
		}	
		
        //$service = new ServiceAPSource($this->getDoctrine()->getEntityManager());
		
		$em = $this->getDoctrine()->getEntityManager();		
		//$actionPlan=$em->getRepository('OptimusOptimusBundle:ActionPlans')->findBy(array("fk_Building"=>$idBuilding, "type"=>4));
		//dump($actionPlan);
		//$idActionPlan=$actionPlan[0]->getId();
		
		$data['dataActionPlan'] = $this->get('service_apsource')->invoke_service($idBuilding);
		
        // invoke service based on last week data
        //$data = $service->invoke_service($idBuilding);
		$data['john'] = 'Hello John!';
		$data['idBuilding']=$idBuilding;
		$data['idAPType']=$idAPType;	
		
        // render the template
        return $this->render('OptimusOptimusBundle:EnergySourceActionPlan:energySourceActionPlan.html.twig', $data);
    }
}