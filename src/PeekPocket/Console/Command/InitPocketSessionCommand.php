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

        $httpClient = new Client();
        $oauthClient = new PocketOAuthClient($httpClient);
        $token = $oauthClient->requestToken($consumerKey);


        $authUrl = $oauthClient->getAuthUrl($token);

        $output->writeln("Visit this url to authorize this app: \n" 
            . $authUrl . "
            then come back.");
        $question = new Question('Press any key to continue...');
        $helper->ask($input, $output, $question);

        $accessToken = $oauthClient->requestAuth($consumerKey, $token);        

        $data = $httpClient->get('https://getpocket.com/v3/get', [
            'query' => [
                'consumer_key' => $consumerKey,
                'access_token' => $accessToken
            ]
        ]);

        die($data->getBody());
        
    }
}
