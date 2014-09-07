<?php
namespace PeekPocket\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class InitPocketSessionCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('initialize-session')
            ->setDescription('Initialize Pocket OAuth session')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $output->writeln('Create a new Pocket app: http://getpocket.com/developer/apps/new');
        $question = new Question('Enter your Consumer Key:');
        $consumerKey = $helper->ask($input, $output, $question);
        $output->writeln('Visit this url to get a token: http://getpocket.com/developer/apps/new \n
            Follow the instructions, at the end you will be redirected to an URL like \n
            peekpocket:...');
        $question = new Question('Copy the url an paste it here:');
        $consumerKey = $helper->ask($input, $output, $question);
        
    }
}
