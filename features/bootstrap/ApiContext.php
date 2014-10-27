<?php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

use org\bovigo\vfs\vfsStream;

use PeekPocket\Credentials;
use PeekPocket\Console\Command\InitPocketSessionCommand;

use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;

/**
 * Behat context class.
 */
class ApiContext implements SnippetAcceptingContext
{
    protected $filesystem;
    protected $credentials;
    protected $root;
    protected $container;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct($root)
    {
        $this->root = $root;
        $this->filesystem = vfsStream::setup('home', null, []);
        $this->container = $this->bootContainer();
    }

    private function bootContainer()
    {

        $container = new ContainerBuilder();
        $container->setParameter('homedir', vfsStream::url('home'));
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('peekpocket.xml');
        $loader->load('peekpocket_test.xml');

        return $container;
    }


    /**
     * @Given there is a credentials file
     */
    public function thereIsACredentialsFile()
    {
        $credentials = "consumer_key: foo\n";
        file_put_contents(vfsStream::url('home/credentials'), $credentials);
    }

    /**
     * @Given the value :arg1 is set to :arg2
     * @Then the value :arg1 must be stored as :arg2
     */
    public function theValueIsSetTo($arg1, $arg2)
    {
        $expected = "{$arg1}: {$arg2}";
        $content = file_get_contents(vfsStream::url('home/credentials'));
        if (strpos($content, $expected) === false) {
            throw new Exception('value not found');
        }
        
    }

    /**
     * @When I load the credentials file
     */
    public function iLoadTheCredentialsFile()
    {
        $this->credentials = new Credentials(vfsStream::url('home/credentials'));
    }

    /**
     * @Given there is no credentials file
     */
    public function thereIsNoCredentialsFile()
    {
        $this->filesystem = vfsStream::setup('home');
    }

    /**
     * @When I save the credentials file
     */
    public function iSaveTheCredentialsFile()
    {
        $this->credentials->saveCredentials();
    }


    /**
     * @Then the consumer key should be :arg1
     */
    public function theConsumerKeyShouldBe($arg1)
    {
        $key = $this->credentials->getConsumerKey();
        if ($key != $arg1) {
            throw new Exception("Expected '{$arg1}', got '{$key}'");
        }

    }

    /**
     * @Given there is an empty credentials file
     */
    public function thereIsAnEmptyCredentialsFile()
    {
        file_put_contents(vfsStream::url('home/credentials'), '');
    }

    /**
     * @When I set the consumer key to :arg1
     */
    public function iSetTheConsumerKeyTo($arg1)
    {
        $this->credentials = new Credentials(vfsStream::url('home/credentials'));
        $this->credentials->setConsumerKey('foo');
    }


    /**
     * @Given I saved an entry with url :arg1
     */
    public function iSavedAnEntryWithUrl($arg1)
    {
        $httpClient = $this->container->get('peekpocket.http_client');

        $mock = new Mock([
            str_replace(
                '##URL##',
                $arg1,
                file_get_contents($this->root . '/fixtures/api-one-item-response.http')),
        ]);
        $httpClient->getEmitter()->attach($mock);
        
    }

    /**
     * @Given there is a initialized Pocket client
     */
    public function thereIsAInitializedPocketClient()
    {
        file_put_contents(vfsStream::url('home/.peekpocketrc'), $this->getSampleCredentials());
    }

    /**
     * @When I ask for the last :arg1 items
     */
    public function iAskForTheLastItems($arg1)
    {
        $pocket = $this->container->get('peekpocket.pocket');
        $this->apiResult = $pocket->fetchItems(5);
    }

    /**
     * @Then the first element of the result array must have url :arg1
     */
    public function theFirstElementOfTheResultArrayMustHaveUrl($arg1)
    {
        $item = $this->apiResult[0];
        assertThat($item->getUrl(), equalTo($arg1));
    }


    protected function getSampleCredentials()
    {
        $sampleCredentials = <<<EOD
consumer_key: some_consumer_key
access_token: some_access_token\n
EOD;
        return $sampleCredentials;

    }
}
