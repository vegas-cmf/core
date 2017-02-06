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

namespace Vegas\Tests\Mvc\View\Engine;

use Phalcon\DI;
use Vegas\Mvc\View;
use Vegas\Test\TestCase;

class VoltTest extends TestCase
{
    public function testHelpers()
    {
        $view = new View();

        $engines = $view->getRegisteredEngines();

        $volt = $engines['.volt'];
        $volt = $volt($view, DI::getDefault());

        $compiler = $volt->getCompiler();

        $this->assertEquals('<?= method_exists($object, \'method\') ?>', $compiler->compileString('{{ methodExists(object, "method") }}'));

        $this->assertEquals('<?= (new \Vegas\Tag\ShortenText())->prepare(\'foo\',100, "...") ?>', $compiler->compileString('{{ shortenText("foo") }}'));
        $this->assertEquals('<?= (new \Vegas\Tag\ShortenText())->prepare(\'foo\',50, \'bar\') ?>', $compiler->compileString('{{ shortenText("foo", 50, "bar") }}'));

        $this->assertEquals('<?= (new \Vegas\Tag\Pagination($this->getDI()))->render($page,array()) ?>', $compiler->compileString('{{ pagination(page) }}'));
        $this->assertEquals('<?= (new \Vegas\Tag\Pagination($this->getDI()))->render($page,[\'class\' => \'test\']) ?>', $compiler->compileString('{{ pagination(page,["class": "test"]) }}'));
    }

    public function testFilters()
    {
        $view = new View();

        $engines = $view->getRegisteredEngines();

        $volt = $engines['.volt'];
        $volt = $volt($view, DI::getDefault());

        $compiler = $volt->getCompiler();
        $this->assertEquals('<?= (string)1 ?>', $compiler->compileString('{{ 1|toString }}'));
    }

    public function testShouldThrowExceptionForUnknownFilter()
    {
        $view = new View();
        $volt = new View\Engine\Volt($view);
        $exception = null;
        try {
            $volt->registerFilter('test');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Mvc\View\Engine\Volt\Exception\UnknownFilterException', $exception);
    }

    public function testShouldThrowExceptionForUnknownHelper()
    {
        $view = new View();
        $volt = new View\Engine\Volt($view);
        $exception = null;
        try {
            $volt->registerHelper('test');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Mvc\View\Engine\Volt\Exception\UnknownHelperException', $exception);
    }
}