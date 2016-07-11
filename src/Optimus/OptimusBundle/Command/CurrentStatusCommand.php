<?php

namespace Optimus\OptimusBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CurrentStatusCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('Current status')
            ->setDescription('Creation of the current status')            
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$output->writeln("------** Current Status **--------");
		
		$aEvents=$this->getContainer()->get('service_tracker')->currentStatusTracker();
		
		
		
	}
}