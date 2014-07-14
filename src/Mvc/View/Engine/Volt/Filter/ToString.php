<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Mvc\View\Engine\Volt\Filter;


use Vegas\Mvc\View\Engine\Volt\VoltFilterAbstract;

/**
 * Class ToString
 * @package Vegas\Mvc\View\Volt\Filter
 */
class ToString extends VoltFilterAbstract
{

    /**
     * Casts object to string
     *
     * @return callable
     */
    public function getFilter()
    {
        return function($resolvedArgs, $exprArgs) {
            return sprintf('(string)%s', $resolvedArgs);
        };
    }
}