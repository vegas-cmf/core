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
namespace Vegas\Tests\Assets;

use Phalcon\DI;
use Vegas\Assets\Manager;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $di;

    protected function setUp()
    {
        $this->di = DI::getDefault();
    }

    public function testCssAdding()
    {
        $manager = new Manager();

        $this->assertEquals(0, $manager->getCss()->count());

        $manager->addCss('something/example.css');
        $this->assertEquals(1, $manager->getCss()->count());

        $manager->addCss('something/other.css');
        $this->assertEquals(2, $manager->getCss()->count());

        $manager->addCss('something/other.css');
        $this->assertEquals(2, $manager->getCss()->count());
        $this->assertEquals(0, $manager->getJs()->count());
    }

    public function testJsAdding()
    {
        $manager = new Manager();

        $this->assertEquals(0, $manager->getJs()->count());

        $manager->addJs('something/example.js');
        $this->assertEquals(1, $manager->getJs()->count());

        $manager->addJs('something/other.js');
        $this->assertEquals(2, $manager->getJs()->count());

        $manager->addJs('something/other.js');
        $this->assertEquals(2, $manager->getJs()->count());
        $this->assertEquals(0, $manager->getCss()->count());
    }
}
