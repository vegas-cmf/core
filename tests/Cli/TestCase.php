<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Cli;

use Phalcon\DI;
use Vegas\Cli\Bootstrap;
use Vegas\Mvc\Controller\Crud;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $bootstrap;
    protected $di;

    public function setUp()
    {
        $this->di = DI::getDefault();

        $bootstrap = new Bootstrap($this->di->get('config'));

        $this->bootstrap = $bootstrap;
    }
}
