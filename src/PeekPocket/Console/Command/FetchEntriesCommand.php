<?php
namespace PeekPocket\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use GuzzleHttp\Client;
use PeekPocket\Credentials;

class FetchEntriesCommand extends Command
{
    private $credentials;
    private $httpClient;

    public function __construct($credentials, $httpClient)
    {
        $this->credentials = $credentials;
        $this->httpClient = $httpClient;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('fetch')
            ->setDescription('Fetch pocket entries')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accessToken = $this->credentials->getAccessToken();
        $consumerKey = $this->credentials->getConsumerKey();

        if (!$accessToken || !$consumerKey) {
            $output->writeln("Access token not found, please run initialize-session.");
            return;
        }


        $data = $this->httpClient->get('https://getpocket.com/v3/get', [
            'query' => [
                'consumer_key' => $consumerKey,
                'access_token' => $accessToken,
                'state' => 'all'
            ]
        ]);

        if ($data->getStatusCode() == 200) {
            $entries = json_decode($data->getBody());
            print_r($entries);
        } 
    }
}
