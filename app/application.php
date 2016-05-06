<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Optimus\OptimusBundle\Console\TestCronJob;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new TestCronJob());
$application->run();