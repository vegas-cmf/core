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

namespace Vegas\Tests\DI;

use Phalcon\DI;
use Vegas\DI\Scaffolding\Adapter;


class MongoTest extends \PHPUnit_Framework_TestCase
{
    public function testScaffoldingImplementsCorrectAbstract()
    {
        $di = DI::getDefault();
        $di->set('scaffolding', new \Vegas\DI\Scaffolding(new Adapter\Mongo()));
        
        $this->assertInstanceOf('\Vegas\DI\Scaffolding\AdapterInterface', $di->get('scaffolding')->getAdapter());
    }
}
