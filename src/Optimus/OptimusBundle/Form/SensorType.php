<?php

namespace Optimus\OptimusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SensorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
        $builder				
				->add('name', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('color', 'text', array("attr"=> array('class' => 'fieldClass'), 'required' => false))
				->add('display', 'text', array("attr"=> array('class' => 'fieldClass'), 'required' => false))
				->add('urlService', 'text', array("attr"=> array('class' => 'fieldClass'), 'required' => false))
				->add('url', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('description', 'text', array("attr"=> array('class' => 'fieldClass'), 'required' => false))
				->add('predictionmodelparameters', 'text', array("attr"=> array('class' => 'fieldClass'), 'required' => false))
				->add('units', 'text', array("attr"=> array('class' => 'fieldClass'), 'required' => false))
				->add('aggregation', 'choice', array(
						'choices' => array(
								'SUM' => 'SUM',
								'AVG' => 'AVG',
								'SAMPLE' => 'LAST',
								'BOOLEAN' => 'BOOLEAN',
								'DEDUCTION' => 'DEDUCTION'
						),
						'multiple' => false,
						'expanded' => true,
						'required' => true		
					))
				
				;
    }

    public function getName()
    {
        return 'optimus_optimusbundle_sensortype';
    }
}
