<?php
use Vegas\Cli\Task\Option;

/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CustomTask extends \Vegas\Cli\Task
{

    public function initialize()
    {
        $action = new \Vegas\Cli\Task\Action('test', 'Test action');

        //foo option
        $foo = new Option('foo', 'f', 'Foo option');
        $action->addOption($foo);
        $this->addAction($action);
    }

    public function testAction($params)
    {
        $this->validate($params);
        $this->putText(1234);
    }

    public function getOptions()
    {
        // TODO: Implement getOptions() method.
    }
}