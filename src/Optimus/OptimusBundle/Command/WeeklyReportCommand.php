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
			->addArgument(
                'option',
                InputArgument::OPTIONAL,
                'do you want to update the figures of all weekly reports?'
            )			
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$option = $input->getArgument('option');
		
		$output->writeln("------** Weekly Report  ".$option." **--------");
		
		if(strcmp(strtolower($option), "update") == 0 ) {
			$aEvents=$this->getContainer()->get('service_weeklyReport')->updateWeeklyReports();
		}
		else if(strcmp(strtolower($option), "statistics") == 0 ) {
			$aEvents=$this->getContainer()->get('service_weeklyReport')->statisticsReports();
		}
		else {
			$aEvents=$this->getContainer()->get('service_weeklyReport')->createWeeklyReport();
		}
		
		
		
	}
}