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
class CliContext implements SnippetAcceptingContext
{
    protected $filesystem;
    protected $credentials;
    protected $root;
    protected $output;
    protected $input;
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
     * @Given there are no stored credentials
     */
    public function thereAreNoStoredCredentials()
    {
        file_put_contents(vfsStream::url('home/credentials'), '');
        $credentials = new Credentials(vfsStream::url('home/credentials'));
        $key = $credentials->getConsumerKey();
        if ($key != '') {
            throw new Exception('Stored credentials found!');
        }

    }

    /**
     * @When I launch the application
     */
    public function iLaunchTheApplication()
    {
        throw new PendingException();
    }

    /**
     * @Then I got instructions to create a new app
     */
    public function iGotInstructionsToCreateANewApp()
    {
        $output = $this->commandTester->getDisplay();
        $expected = "Create a new Pocket app";
        assertThat($output, containsString($expected));
    }

    /**
     * @Then I got asked the consumer key
     */
    public function iGotAskedTheConsumerKey()
    {
        $output = $this->commandTester->getDisplay();
        $expected = "Enter your Consumer Key:";
        assertThat($output, containsString($expected));
    }

    /**
     * @Then I got asket to confirm authorization
     */
    public function iGotAsketToConfirmAuthorization()
    {
        $output = $this->commandTester->getDisplay();
        $expected = "Visit this url to authorize this app:";
        assertThat($output, containsString($expected));
        $expected = "http://foo";
        assertThat($output, containsString($expected));
    }


    /**
     * @Then credentials are stored
     */
    public function credentialsAreStored()
    {
        $credentials = new Credentials(vfsStream::url('home/.peekpocketrc'));
        assertThat($credentials->getConsumerKey(), equalTo($this->input[0]));
        assertThat($credentials->getAccessToken(), equalTo('foo'));
    }


    /**
     * @Given there is no credentials file
     */
    public function thereIsNoCredentialsFile()
    {
        $this->filesystem = vfsStream::setup('home');
    }

    /**
     * @When I launch the application without parameters
     */
    public function iLaunchTheApplicationWithoutParameters()
    {
        $this->output = shell_exec($this->root . '/peekpocket --no-ansi');
    }

    /**
     * @Then I get an help message
     */
    public function iGetAnHelpMessage()
    {
        assertThat($this->output, startsWith('PeekPocket version'));
    }

    /**
     * @When I launch the command :arg1 with input:
     */
    public function iLaunchTheCommand($arg1, TableNode $table)
    {
        $this->input = array();
        $application = $this->getApplication();

        $command = $application->find($arg1);
        $this->commandTester = new CommandTester($command);
        $helper = $command->getHelper('question');
        $stream = '';
        foreach ($table as $row) {
            $stream .= $row['INPUT'] . "\n";
            $this->input[] = $row['INPUT'];
        }
        $helper->setInputStream($this->getInputStream($stream));
        $this->commandTester->execute(array('command' => $command->getName()));
    }

    protected function getApplication()
    {
        $container = $this->container;

        return $container->get('symfony.application');
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
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
