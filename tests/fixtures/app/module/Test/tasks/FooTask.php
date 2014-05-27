<?php
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
 
namespace Test\Tasks;

class FooTask extends \Vegas\Cli\Task
{
    public function testAction()
    {
        echo 'FOO';
    }

    /**
     * Task must implement this method to set available options
     *
     * @return mixed
     */
    public function setOptions()
    {
        // TODO: Implement setOptions() method.
    }
} 