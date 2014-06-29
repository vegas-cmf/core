<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Cli;

use Phalcon\DI;
use Vegas\Cli\Bootstrap;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    protected $di;

    protected function setUp()
    {
        $this->di = DI::getDefault();
    }

    public function testInvalidTask()
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
            1 => 'foo'
        ));

        try {
            $cli->setup()->run();
            throw new \Exception('Bad exception.');
        } catch (\Exception $ex) {
            $this->assertInstanceOf('Vegas\Cli\Exception\TaskActionNotSpecifiedException', $ex);
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

    public function testValidAppTask()
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

    public function testValidHelp()
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
        $returnedValue = base64_encode(ob_get_contents());

        ob_end_clean();

        $this->assertEquals('G1swOzMybVRlc3QgYWN0aW9uG1swbQoKVXNhZ2U6ChtbMTszMG0gICBhcHA6Y3VzdG9tIHRlc3QgW29wdGlvbnNdG1swbQoKT3B0aW9uczobWzBtChtbMTszMm0gICAtLWZvbyAgICAgLWYgICAgICBGb28gb3B0aW9uLiBVc2FnZSBhcHA6Y3VzdG9tIHRlc3QgLWYgbnVtYmVyT2ZTdGgbWzBtCg==', $returnedValue);
    }

    public function testValidValues()
    {
        $correctBase = 'G1sxOzM0bRtbMG0KG1sxOzM0bTEyMxtbMG0K';

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
        $returnedValue = base64_encode(ob_get_contents());

        ob_end_clean();

        $this->assertEquals($correctBase, $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '--f',
            4 => 123
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = base64_encode(ob_get_contents());

        ob_end_clean();

        $this->assertEquals($correctBase, $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '-f=123'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = base64_encode(ob_get_contents());

        ob_end_clean();

        $this->assertEquals($correctBase, $returnedValue);

        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:custom',
            2 => 'test',
            3 => '--f=123'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = base64_encode(ob_get_contents());

        ob_end_clean();

        $this->assertEquals($correctBase, $returnedValue);
    }

    public function testValidModuleTask()
    {
        $cli = new Bootstrap($this->di->get('config'));
        $cli->setArguments(array(
            0 => 'cli/cli.php',
            1 => 'app:test:foo',
            2 => 'test'
        ));

        ob_start();

        $cli->setup()->run();
        $returnedValue = base64_encode(ob_get_contents());

        ob_end_clean();

        $this->assertEquals('Rk9P', $returnedValue);
    }
}
