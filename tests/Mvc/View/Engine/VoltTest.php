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
namespace Vegas\Tests\Mvc\View\Engine;

use Phalcon\DI;
use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Vegas\Mvc\View;

class VoltTest extends \PHPUnit_Framework_TestCase
{
    public function testHelpers()
    {
        $view = new View();

        $engines = $view->getRegisteredEngines();

        $volt = $engines['.volt'];
        $volt = $volt($view, DI::getDefault());

        $compiler = $volt->getCompiler();

        $this->assertEquals('<?php echo (new \Vegas\Tag\ShortenText())->prepare(\'foo\',100, "..."); ?>', $compiler->compileString('{{ shortenText("foo") }}'));
        $this->assertEquals('<?php echo (new \Vegas\Tag\ShortenText())->prepare(\'foo\',50, \'bar\'); ?>', $compiler->compileString('{{ shortenText("foo", 50, "bar") }}'));

        $this->assertEquals('<?php echo (new \Vegas\Tag\Pagination($this->getDI()))->render($page,array()); ?>', $compiler->compileString('{{ pagination(page) }}'));
        $this->assertEquals('<?php echo (new \Vegas\Tag\Pagination($this->getDI()))->render($page,array(\'class\' => \'test\')); ?>', $compiler->compileString('{{ pagination(page,["class": "test"]) }}'));
    }
}