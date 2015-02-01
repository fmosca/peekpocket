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
                'count' => 5,
                'detailType' => 'complete',
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
                'count' => 5,
                'detailType' => 'complete',
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
                'since' => $since->format('U'),
                'detailType' => 'complete',
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

    function it_returns_an_array_of_items_by_date($credentials, $httpClient, ResponseInterface $response)
    {
        $since = new \DateTime('yesterday');

        $credentials->getConsumerKey()->willReturn('foo');
        $credentials->getAccessToken()->willReturn('bar');
        $httpClient->post("https://getpocket.com/v3/get", [
            'body' => [
                'consumer_key' => 'foo',
                'access_token' => 'bar',
                'since' => $since->format('U'),
                'detailType' => 'complete',
                ]
            ])
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getStatusCode()
            ->willReturn(200);
        
        $sampleResponse = $this->createEmptyPocketResponse();
        $this->addDatedItems($sampleResponse, 3, 'yesterday');
        $this->addDatedItems($sampleResponse, 1, 'today');
        $response->getBody()
            ->willReturn($this->jsonResponse($sampleResponse));

        $this->fetchItemsByDate($since)->shouldHaveCount(3);
    }

    private function sampleOneItemList()
    {
        list($header, $body) = preg_split('/\n\n/', file_get_contents(__DIR__ . '/../../fixtures/api-1-items-response.http'));
        return $body;
        
    }

    private function createEmptyPocketResponse()
    {
        return ['status' => 1, 'complete' => 1, 'list' => []];
    }

    private function addDatedItems(&$response, $count, $dateString)
    {
        for ($i=0; $i<$count;$i++) { 
            $response['list'][] = [
                'resolved_url' => 'http://example.com/' . md5($dateString) . '-' . $i,
                'resolved_title' => "Example {$i} {$dateString}",
                'excerpt' => '',
                'status' => 0,
                'time_added' => (new \DateTime($dateString . ' noon'))->format('U'),
                'time_read' => 0,
                ];
        }
    }

    private function jsonResponse($response)
    {
        return json_encode($response);
    }

}
