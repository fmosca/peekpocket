<?php

namespace spec\PeekPocket;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;

use PeekPocket\Credentials;

class PocketClientSpec extends ObjectBehavior
{
    function let(ClientInterface $httpClient, Credentials $credentials)
    {
        $this->beConstructedWith($credentials, $httpClient);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PeekPocket\PocketClient');
    }
    
    function it_returns_an_empty_array_if_no_items($credentials, $httpClient, ResponseInterface $response)
    {
        $credentials->getConsumerKey()->willReturn('foo');
        $credentials->getAccessToken()->willReturn('bar');
        $httpClient->post("https://getpocket.com/v3/get", [
            'body' => [
                'consumer_key' => 'foo',
                'access_token' => 'bar',
                'count' => 5
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn('{"status":1,"list":{}}');

        $this->fetchItems(5)->shouldReturn([]);
    }

    function it_returns_an_array_of_items_if_at_least_one($credentials, $httpClient, ResponseInterface $response)
    {
        $credentials->getConsumerKey()->willReturn('foo');
        $credentials->getAccessToken()->willReturn('bar');
        $httpClient->post("https://getpocket.com/v3/get", [
            'body' => [
                'consumer_key' => 'foo',
                'access_token' => 'bar',
                'count' => 5
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn($this->sampleOneItemList());

        $this->fetchItems(5)->shouldHaveCount(1);
    }

    function it_returns_an_array_of_items_since($credentials, $httpClient, ResponseInterface $response)
    {
        $since = new \DateTime('yesterday');
        $credentials->getConsumerKey()->willReturn('foo');
        $credentials->getAccessToken()->willReturn('bar');
        $httpClient->post("https://getpocket.com/v3/get", [
            'body' => [
                'consumer_key' => 'foo',
                'access_token' => 'bar',
                'since' => $since->format('U')
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->willReturn(200);
        $response->getBody()
            ->willReturn($this->sampleOneItemList());

        $this->fetchItemsSince($since)->shouldHaveCount(1);
    }

    private function sampleOneItemList()
    {
        list($header, $body) = preg_split('/\n\n/', file_get_contents(__DIR__ . '/../../fixtures/api-one-item-response.http'));
        return $body;
        
    }
}
