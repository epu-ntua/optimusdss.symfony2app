<?php

namespace Optimus\OptimusBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
        $builder
				->add('name', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('city', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('street', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('use_building', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('year_of_construction', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('surface', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('occupation', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('energy_rating', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('electricity_consumption', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('gas_consumption', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('energy_production_from_RES', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('electricity_energy_cost', 'text', array("attr"=> array('class' => 'fieldClass')))
				->add('gas_energy_cost', 'text', array("attr"=> array('class' => 'fieldClass')))
				
				;
    }

    public function getName()
    {
        return 'optimus_optimusbundle_buildingtype';
    }
}
