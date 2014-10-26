<?php

namespace PeekPocket;

use GuzzleHttp\ClientInterface;

class PocketClient
{

    private $credentials;
    private $httpClient;
    
    public function __construct(Credentials $credentials, ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->credentials = $credentials;
        
    }

    public function fetchItems($count)
    {
        $result = $this->apiCall('get', [
            'count' => $count
            ]);

        return $this->fetchItemsFromApiResult($result);
    }

    public function fetchItemsSince(\DateTime $since)
    {
        $result = $this->apiCall('get', [
            'since' => $since->format('U')
            ]);

        return $this->fetchItemsFromApiResult($result);

    }

    private function apiCall($method, $args)
    {
        $defaultBody = [
                'consumer_key' => $this->credentials->getConsumerKey(),
                'access_token' => $this->credentials->getAccessToken(),
                ];
        $data = $this->httpClient->post("https://getpocket.com/v3/{$method}", [
            'body' => array_merge($defaultBody, $args)
            ]);

        $result = json_decode($data->getBody(), true);

        return $result;
    }

    private function fetchItemsFromApiResult($result)
    {
        $items = array();
        foreach($result['list'] as $item) {
            $items[] = PocketItem::buildFromArray($item);
        }

        return $items;
    }
}
