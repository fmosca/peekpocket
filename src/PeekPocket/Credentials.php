<?php
namespace PeekPocket;

use PeekPocket\Exception\CredentialsNotFoundException;

class Credentials
{
    private $path;
    private $credentials;

    public function __construct($path)
    {
        $this->path = $path;

        if (!file_exists($path)) {
            touch($path);
        }

        $this->credentials = $this->parseCredentials(file_get_contents($path));
    }

    public function parseCredentials($content)
    {
        $credentials = [];
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (trim($line) != '') {
                list($key, $value) = array_map('trim', explode(":", $line));
                $credentials[$key] = $value;
            }
        }

        return $credentials;
    }

    public function getConsumerKey()
    {
        if (isset($this->credentials['consumer_key'])) {
            return $this->credentials['consumer_key'];
        }
    }

    public function setConsumerKey($value)
    {
        $this->credentials['consumer_key'] = $value;
        return $this;
    }

    public function getAccessToken()
    {
        if (isset($this->credentials['access_token'])) {
            return $this->credentials['access_token'];
        }
    }

    public function setAccessToken($value)
    {
        $this->credentials['access_token'] = $value;
        return $this;
    }

    public function storeCredentials($consumerKey, $accessToken)
    {
        $this->setConsumerKey($consumerKey);
        $this->setAccessToken($accessToken);
        return $this->saveCredentials();
    }
    
    public function saveCredentials()
    {
        $file = new \SplFileObject($this->path, 'w');
        foreach ($this->credentials as $key => $value) {
            $file->fwrite("{$key}: {$value}\n");
        }

        return true;
    }
}
