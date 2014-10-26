<?php

namespace spec\PeekPocket;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use org\bovigo\vfs\vfsStream;

class CredentialsSpec extends ObjectBehavior
{
    protected $filesystem;

    function let()
    {
        $credentials = "consumer_key: foo\naccess_token: bar\n";
        $this->filesystem = vfsStream::setup('home', null, ['credentials' => $credentials]);

        $this->beConstructedWith(vfsStream::url('home/credentials'));

    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PeekPocket\Credentials');
    }

    function it_parses_credentials()
    {
        $this->parseCredentials("consumer_key: foo")->shouldReturn(
            ['consumer_key' => 'foo']
        );
    }

    function it_stores_credentials()
    {
        $this->storeCredentials('some_consumer_key', 'some_access_token')->shouldReturn(true);

        $content = file_get_contents(vfsStream::url('home/credentials'));

        $expected = <<<EOD
consumer_key: some_consumer_key
access_token: some_access_token\n
EOD;

        assertThat($content, equalTo($expected));

    }

    function it_reads_the_consumer_key()
    {
        $this->getConsumerKey()->shouldReturn('foo');
    }

    function it_writes_the_consumer_key()
    {
        $this->setConsumerKey('foo')->shouldReturn($this);
    }

    function it_gives_auth_pair()
    {
        $this->getAuthPair()->shouldReturn(
            ['consumer_key' => 'foo',
            'access_token' => 'bar']);
    }
}
