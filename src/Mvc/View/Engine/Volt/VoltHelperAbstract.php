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
 
namespace Vegas\Mvc\View\Engine\Volt;

use Phalcon\Mvc\View\Engine\Volt\Compiler;

/**
 * Class VoltHelperAbstract
 * @package Vegas\Mvc\View\Engine\Volt
 */
abstract class VoltHelperAbstract
{
    /**
     * View compiler
     *
     * @var Compiler
     */
    protected $compiler;

    /**
     * Returns helper function
     *
     * @return callable
     */
    abstract public function getHelper();

    /**
     * Constructor
     * Sets view compiler
     *
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
    }
}
