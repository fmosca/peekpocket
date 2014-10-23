<?php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

require __DIR__.'/../vendor/autoload.php';

$container = new ContainerBuilder();
$container->setParameter('homedir', getenv('HOME'));
$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../config'));
$loader->load('peekpocket.xml');

$output = $container->get('symfony.console_output');

$application = $container->get('symfony.application');
$application->run(null, $output);

/*
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

use PeekPocket\Console\Command\InitPocketSessionCommand;
use PeekPocket\Console\Command\FetchEntriesCommand;


$application = new Application('Peekpocket', '0.1');
$application->add(new InitPocketSessionCommand());
$application->add(new FetchEntriesCommand());
$application->run();
 */
