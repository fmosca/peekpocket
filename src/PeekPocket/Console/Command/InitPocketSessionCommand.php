<?php
namespace PeekPocket\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use GuzzleHttp\Client;
use PeekPocket\PocketOAuthClient;
use PeekPocket\Credentials;

class InitPocketSessionCommand extends Command
{
    private $credentials;
    private $pocketOAuthClient;

    public function __construct($credentials, $pocketOAuthClient)
    {
        $this->credentials = $credentials;
        $this->pocketOAuthClient = $pocketOAuthClient;
        parent::__construct();
    }

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
        $question = new Question('Enter your Consumer Key: ');
        $consumerKey = $helper->ask($input, $output, $question);

        $token = $this->pocketOAuthClient->requestToken($consumerKey);
        $authUrl = $this->pocketOAuthClient->getAuthUrl($token);

        $output->writeln("Visit this url to authorize this app: \n" 
            . $authUrl . "\n" 
            . "then come back.");
        $question = new Question('Press enter to continue...');
        $helper->ask($input, $output, $question);

        $accessToken = $this->pocketOAuthClient->requestAuth($consumerKey, $token);        

        $this->credentials->storeCredentials($consumerKey, $accessToken);
        
        $output->writeln("Done.");
        
    }

}
