<?php

namespace Optimus\OptimusBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

//use Symfony\Component\Entity\Sensor;

class BuildingSensorsRTimeType extends AbstractType
{
	private $options = array();
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
        $builder
				->add('name', 'hidden', array("attr"=> array('class' => 'fieldClass')))
				->add('fk_sensor', 'entity', array(
						'class' => 'Optimus\OptimusBundle\Entity\Sensor',
						'property' => 'name',
						'label'          => 'Select sensor',
						'query_builder' => function (EntityRepository $er) {
							return $er->createQueryBuilder('u')								
									->where("u.fk_Building = :fk_Building ") // and u.display='yes'
									->setParameter('fk_Building', $this->options['attr']['idBuilding']);
						}, 
						"attr"=> array('class' => 'fieldClass')
				));
		//dump("hello");
    }
	
	/*public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Optimus\OptimusBundle\Entity\APSensors'
        ));
    }*/

    public function getName()
    {
        return 'optimus_optimusbundle_buildingsensorsrtimetype';
    }
	
	public function __construct(array $options)
    {
        $this->options = $options;
    }
}
