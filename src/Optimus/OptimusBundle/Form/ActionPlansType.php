<?php

namespace Optimus\OptimusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ActionPlansType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
        $builder				
				->add('status', 'choice', array("attr"=> array('class' => 'fieldClass'), 'choices'  => array('0' => '0', '1' => '1'), 'required' => false))
				;
    }

    public function getName()
    {
        return 'optimus_optimusbundle_actionplantype';
    }
}
