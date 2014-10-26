<?php

namespace PeekPocket;

class MockPocketOAuthClient
{
    public function requestToken($consumerKey)
    {
        return 'foo';
    }

    public function getAuthUrl($token)
    {
        return 'http://foo';
    }

    public function requestAuth($consumerKey, $requestToken)
    {
        return 'foo';
    }
}
