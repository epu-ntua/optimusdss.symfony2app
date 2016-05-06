<?php

namespace Optimus\OptimusBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class SensorStatusCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('Sensor status')
            ->setDescription('Looking for the status of the sensors registered in OPTIMUS')            
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$output->writeln("------** Sensor Status**--------");
		
		$aEvents=$this->getContainer()->get('service_sensorStatus')->checkStatus();
		
		
		
	}
}