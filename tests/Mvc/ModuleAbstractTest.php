<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Tests\Mvc;

use Phalcon\DI;
use Vegas\Mvc\Controller\Crud;
use Vegas\Test\TestCase;

class ModuleAbstractTest extends TestCase
{
    public function testModuleAutoloaders()
    {
        $this->assertTrue(class_exists('Test\Services\Fake'));
        $this->assertTrue(class_exists('Test\Models\Fake'));
    }

    public function testModuleHandling()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';
        $_SERVER['REQUEST_URI'] = '/test/fake/test';

        $this->assertEquals('Test view', $this->bootstrap->run('/test/fake/test'));

        $this->assertTrue(class_exists('Test\Controllers\Backend\FakeController'));
        $this->assertTrue(class_exists('Test\Forms\Fake'));
    }

    public function testNoNamespaceModuleHandling()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';
        $_SERVER['REQUEST_URI'] = '/test/short/namespace/test';

        $this->assertEquals('12shortPartialShort', $this->bootstrap->run('/test/short/namespace/test'));

        $this->assertTrue(class_exists('Test\Controllers\FakeController'));
        $this->assertTrue(class_exists('Test\Forms\Fake'));
    }
} 