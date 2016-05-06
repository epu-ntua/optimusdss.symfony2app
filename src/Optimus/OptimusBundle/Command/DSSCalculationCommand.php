<?php

namespace Optimus\OptimusBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class DSSCalculationCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('DSS calculation')
            ->setDescription('Invoking prediction models and Action plans suggestion')            
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$output->writeln("------** DSS calculation **--------");
		$output->writeln("");
		
		//$aEvents=$this->getContainer()->get('service_sensorStatus')->checkStatus();
		
		
		//$ip=$this->getContainer()->get('request_stack')->getCurrentRequest()->getClientIp();
		$ip = "127.0.0.1";

		$this->getContainer()->get('service_calculo')->createPredictionAndCalculatesAllBuildings($ip, null); // <--- !!!!!!! TEMP

		$output->writeln("");
		$output->writeln("------** END **--------");
	
	}
}