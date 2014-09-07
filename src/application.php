<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

use PeekPocket\Console\Command\InitPocketSessionCommand;


$application = new Application('Peekpocket', '0.1');
$application->add(new InitPocketSessionCommand());
$application->run();
