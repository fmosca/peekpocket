<?php

namespace spec\PeekPocket;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class PocketOAuthClientSpec extends ObjectBehavior
{
    private $consumerKey;

    function let(Client $httpClient)
    {
        $this->consumerKey = 'foo';
        $this->beConstructedWith($httpClient);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PeekPocket\PocketOAuthClient');
    }

    function it_obtains_a_request_token($httpClient, Response $response)
    {
        $httpClient->post("https://getpocket.com/v3/oauth/request", [
            'body' => [
                'consumer_key' => $this->consumerKey,
                'redirect_uri' => 'http://getpocket.com'
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn('code=abc123def');

        $this->requestToken($this->consumerKey)->shouldReturn('abc123def');
    }

    function it_fails_obtaining_a_request_token($httpClient, Response $response)
    {
        $httpClient->post("https://getpocket.com/v3/oauth/request", [
            'body' => [
                'consumer_key' => $this->consumerKey,
                'redirect_uri' => 'http://getpocket.com'
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->shouldBeCalled()
            ->willReturn(400);

        $this->shouldThrow('\Exception')
            ->during('requestToken', array($this->consumerKey));
    }
    
    function it_obtains_an_auth_token($httpClient, Response $response)
    {
        $httpClient->post("https://getpocket.com/v3/oauth/authorize", [
            'body' => [
                'consumer_key' => $this->consumerKey,
                'code' => 'foo'
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn('access_token=abc123def&username=fmosca%40gmail.com');

        $this->requestAuth($this->consumerKey, 'foo')->shouldReturn('abc123def');
    }

    function it_returns_an_auth_url()
    {
        $url = "https://getpocket.com/auth/authorize?request_token=foo&redirect_uri=http://getpocket.com";

        $this->getAuthUrl('foo')->shouldReturn($url);
    }

}
