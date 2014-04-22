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
 
namespace Vegas\Mvc\View\Engine\Volt;


use Phalcon\Mvc\View\Engine\Volt\Compiler;

abstract class VoltHelperAbstract
{
    /**
     * @var Compiler
     */
    protected $compiler;

    /**
     * @return callable
     */
    abstract public function getHelper();

    /**
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
} 