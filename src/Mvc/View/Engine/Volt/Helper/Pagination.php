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
 
namespace Vegas\Mvc\View\Engine\Volt\Helper;


use Vegas\Mvc\View\Engine\Volt\VoltHelperAbstract;

/**
 * Class Pagination
 * @package Vegas\Mvc\View\Volt\Helper
 */
class Pagination extends VoltHelperAbstract
{

    /**
     * @return callable
     */
    public function getHelper()
    {
        return function($resolvedArgs, $exprArgs) {
            $page = $this->compiler->expression($exprArgs[0]['expr']);
            $options = array();

            if (isset($exprArgs[1])) {
                $options = $this->compiler->expression($exprArgs[1]['expr']);
            }

            $optionString = 'array(';
            foreach ($options As $key => $option) {
                $optionString .= '"'.$key.'" => '.$option;
            }
            $optionString .= ')';

            return '\Vegas\Tag::pagination('.$page.','.$optionString.')';
        };
    }
}