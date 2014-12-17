<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Cli;

use Phalcon\DI;
use Vegas\Cli\Bootstrap;

class BootstrapTest extends TestCase
{
    protected $di;

    public function setUp()
    {
        $this->di = DI::getDefault();
    }

    public function testShouldChangeDI()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $this->assertInstanceOf('\Phalcon\DI\FactoryDefault\CLI', $cli->getDI());
        $cli->setDI($this->di);
        $this->assertInstanceOf(get_class($this->di), $cli->getDI());
    }

    public function testShouldThrowExceptionAboutNotFoundTask()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php'
        ));

        try {
            $cli->setup()->run();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $ex);
        }

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'foo',
            2 => 'bar'
        ));

        try {
            $cli->setup()->run();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $ex);
        }

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'foo:bar',
            2 => 'b'
        ));

        try {
            $cli->setup()->run();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $ex);
        }

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:bar',
            2 => 'foo'
        ));

        try {
            $cli->setup()->run();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Phalcon\CLI\Dispatcher\Exception', $ex);
        }
    }

    public function testShouldThrowExceptionAboutMissingArguments()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test'
        ));

        try {
            $cli->setup()->run();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Task\Exception\MissingRequiredArgumentException', $ex);
        }
    }

    public function testShouldThrowExceptionAboutInvalidArgument()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f',
            4 => 'string'
        ));

        try {
            $cli->setup()->run();
        } catch (\Exception $ex) {
            $this->assertTrue((bool)strstr($ex->getMessage(),'Invalid argument'));
        }
    }

    public function testShouldThrowExceptionAboutInvalidOption()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-d',
            4 => 'string'
        ));

        try {
            $cli->setup()->run();
        } catch (\Exception $ex) {
            $this->assertTrue((bool)strstr($ex->getMessage(),'Invalid option'));
        }
    }

    public function testShouldReturnTaskHelp()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-h'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('Usage', $returnedValue);
        $this->assertContains('Options', $returnedValue);
    }

    public function testShouldReturnTaskHelpWhenNoActionSpecified()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();
        ob_end_clean();

        $this->assertContains('Available actions', $returnedValue);
    }

    public function testShouldExecuteApplicationTask()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f',
            4 => 123
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('123', $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '--foo',
            4 => 123
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('123', $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f=123'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('123', $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '--foo=123'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('123', $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'testArg',
            3 => '999'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('999', $returnedValue);
    }

    public function testShouldExecuteModuleTask()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:test:foo',
            2 => 'test'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('FOO', $returnedValue);
    }

    public function testShouldReturnTextInResponse()
    {
        $colorsOutput = $this->getMockForTrait('\Vegas\Cli\ColorsOutputTrait');

        $runTask = function($action) {
            $cli = new Bootstrap($this->di->get('config'));
            $cli->setArguments(array(
                0 => 'cli/cli.php',
                1 => 'app:custom',
                2 => $action
            ));

            ob_start();
            $cli->setup()->run();
            $returnedValue = ob_get_contents();

            ob_end_clean();

            return $returnedValue;
        };
        $this->assertContains($colorsOutput->getColoredString('Error message', 'red'), $runTask('testError'));
        $this->assertContains($colorsOutput->getColoredString('Warning message', 'yellow'), $runTask('testWarning'));
        $this->assertContains($colorsOutput->getColoredString('Success message', 'green'), $runTask('testSuccess'));
        $this->assertContains($colorsOutput->getColoredString('Some text', 'light_blue'), $runTask('testText'));
        $this->assertContains(print_r(['key' => 'value'], true), $runTask('testObject'));
    }

    public function testShouldLoadLibraryTask()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:cache',
            2 => 'clean'
        ));

        ob_start();
        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('Cleaning cache', $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:fake:fake',
            2 => 'test'
        ));

        ob_start();
        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('Vegas\Fake\Task\FakeTask', $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'vegas:fake_nested:fake',
            2 => 'test'
        ));

        ob_start();
        $cli->setup()->run();
        $returnedValue = ob_get_contents();

        ob_end_clean();

        $this->assertContains('Vegas\Fake\Nested\Task\FakeTask', $returnedValue);
    }

    public function testShouldThrowExceptionAboutNotExistingModuleTask()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setup();

        //remove Test module
        $application = $cli->getApplication();
        $modules = $application->getModules();

        $modulesWithoutTest = array_merge([], $modules);
        unset($modulesWithoutTest['Test']);
        $application->registerModules($modulesWithoutTest);

        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:test:foo',
            2 => 'test'
        ));

        $exception = null;
        try {
            $cli->run();
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('Vegas\Cli\Exception\TaskNotFoundException', $exception);

        $application->registerModules($modules);
    }
}