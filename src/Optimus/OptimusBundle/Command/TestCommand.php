<?php

namespace Optimus\OptimusBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class TestCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
            ->setName('Test')
            ->setDescription('Testing a cron job')            
        ;
	}	
	
	protected function execute (InputInterface $input, OutputInterface $output)
	{
		$output->writeln("------*****--------");
		
		$aEvents=$this->getContainer()->get('service_event')->lastsEvent(1);
		
		$aEvents=$this->getContainer()->get('service_data_capturing')->getDataFromDate('2015-06-25 10:00:00', '2015-06-22 00:00:00', '2015-07-15 00:00:00', '', 'variable', 1);
		
		for($i=0; $i< count($aEvents); $i++)
		{
			$output->writeln($i);
		}
		
	}
}