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
        $credentials = "consumer_key: foo";
        $this->filesystem = vfsStream::setup('home', null, ['credentials' => $credentials]);

        $this->beConstructedWith(vfsStream::url('home/credentials'));

    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PeekPocket\Credentials');
    }

    function it_throws_an_exception_if_file_doesnt_exists()
    {
        $this->shouldThrow('PeekPocket\Exception\CredentialsNotFoundException')
            ->during('__construct', array(vfsStream::url('home/notfound')));
    }

    function it_parses_credentials()
    {
        $this->parseCredentials("consumer_key: foo")->shouldReturn(
            ['consumer_key' => 'foo']
        );
    }

    function it_stores_credentials()
    {
        $this->setConsumerKey('bar');
        $this->saveCredentials()->shouldReturn(true);

        $content = file_get_contents(vfsStream::url('home/credentials'));

        $expected = "consumer_key: bar\n";
        if (strpos($content, $expected) === false) {
            throw new \Exception('value not found');
        }

    }

    function it_reads_the_consumer_key()
    {
        $this->getConsumerKey()->shouldReturn('foo');
    }

    function it_writes_the_consumer_key()
    {
        $this->setConsumerKey('foo')->shouldReturn($this);
    }
}
