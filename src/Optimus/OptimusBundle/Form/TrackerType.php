<?php

namespace Optimus\OptimusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TrackerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
        $builder
			->add("target_EConsumption", 'text', array("label" => "E.Consumption - User's Target", "attr"=> array('class' => 'fieldClass')))
			->add("potential_EConsumption", 'text', array("label" => "E.Consumption - Potential", "attr"=> array('class' => 'fieldClass')))
			->add("baseline_EConsumption", 'text', array("label" => "E.Consumption - Baseline", "attr"=> array('class' => 'fieldClass')))
			
			->add("target_CO2", 'text', array("label" => "CO2 emissions - User's Target", "attr"=> array('class' => 'fieldClass')))
			->add("potential_CO2", 'text', array("label" => "CO2 emissions - Potential", "attr"=> array('class' => 'fieldClass')))
			->add("baseline_CO2", 'text', array("label" => "CO2 emissions - Baseline", "attr"=> array('class' => 'fieldClass')))
			
			->add("target_ECost", 'text', array("label" => "Energy cost - User's Target", "attr"=> array('class' => 'fieldClass')))
			->add("potential_ECost", 'text', array("label" => "Energy cost - Potential", "attr"=> array('class' => 'fieldClass')))
			->add("baseline_ECost", 'text', array("label" => "Energy cost - Baseline", "attr"=> array('class' => 'fieldClass')))
			
			->add("target_REUse", 'text', array("label" => "Renewable energy use - User's Target", "attr"=> array('class' => 'fieldClass')))
			->add("potential_REUse", 'text', array("label" => "Renewable energy use - Potential", "attr"=> array('class' => 'fieldClass')))
			->add("baseline_REUse", 'text', array("label" => "Renewable energy use - Baseline", "attr"=> array('class' => 'fieldClass')))			
			;
    }

    public function getName()
    {
        return 'optimus_optimusbundle_trackertype';
    }
}
