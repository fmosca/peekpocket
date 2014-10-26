<?php

namespace PeekPocket;

use GuzzleHttp\ClientInterface;

class PocketOAuthClient
{
    const TOKEN_REQUEST_URL = "https://getpocket.com/v3/oauth/request";
    const REDIRECT_URI = "http://getpocket.com";
    const AUTH_URL = "https://getpocket.com/auth/authorize?request_token=%s&redirect_uri=%s";
    const ACCESS_TOKEN_URL = "https://getpocket.com/v3/oauth/authorize";
        
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function requestToken($consumerKey)
    {
        $response = $this->httpClient->post(self::TOKEN_REQUEST_URL, [
            'body' => [
                'consumer_key' => $consumerKey,
                'redirect_uri' => self::REDIRECT_URI
                ]
            ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Error requesting request token');
        }

        
        $token = explode("=", $response->getBody())[1]; 

        return $token;
    }

    public function getAuthUrl($token)
    {
        return sprintf(self::AUTH_URL, $token, self::REDIRECT_URI);
    }

    public function requestAuth($consumerKey, $requestToken)
    {
        $response = $this->httpClient->post(self::ACCESS_TOKEN_URL, [
            'body' => [
                'consumer_key' => $consumerKey,
                'code' => $requestToken
                ]
            ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Error requesting auth token');
        }

        $token = explode("=", explode("&", $response->getBody())[0])[1];

        return $token;

    }
}
