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
namespace Vegas\Tests\Mvc\View;

use Phalcon\DI;
use Vegas\Mvc\View\Simple;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    public function testView()
    {
        $view = new Simple();

        $engines = $view->getRegisteredEngines();

        $this->assertNotEmpty($engines['.volt']);
        $this->assertNotEmpty($engines['.phtml']);

        $volt = $engines['.volt']($view, DI::getDefault());

        $this->assertInstanceOf('\Vegas\Mvc\View\Simple', $volt->getView());
    }
}