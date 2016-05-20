<?php

namespace Optimus\OptimusBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class WeeklyReportCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('Weekly report')
            ->setDescription('Creation of the weekly report')            
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$output->writeln("------** Weekly Report **--------");
		
		$aEvents=$this->getContainer()->get('service_weeklyReport')->createWeeklyReport();
		
		
		
	}
}