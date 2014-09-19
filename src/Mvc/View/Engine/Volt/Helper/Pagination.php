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
 
namespace Vegas\Mvc\View\Engine\Volt\Helper;


use Vegas\Mvc\View\Engine\Volt\VoltHelperAbstract;

/**
 * Class Pagination
 * @package Vegas\Mvc\View\Volt\Helper
 */
class Pagination extends VoltHelperAbstract
{

    /**
     * Creates pagination
     *
     * @return callable
     */
    public function getHelper()
    {
        return function($resolvedArgs, $exprArgs) {
            $page = $this->compiler->expression($exprArgs[0]['expr']);
            $options = 'array()';

            if (isset($exprArgs[1])) {
                $options = $this->compiler->expression($exprArgs[1]['expr']);
            }

            return '(new \Vegas\Tag\Pagination($this->getDI()))->render('.$page.','.$options.')';
        };
    }
}