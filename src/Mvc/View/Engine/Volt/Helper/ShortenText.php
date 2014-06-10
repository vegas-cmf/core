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
 * Class ShortenText
 * @package Vegas\Mvc\View\Volt\Helper
 */
class ShortenText extends VoltHelperAbstract
{

    /**
     * @return callable
     */
    public function getHelper()
    {
        return function($resolvedArgs, $exprArgs) {
            $text = $this->compiler->expression($exprArgs[0]['expr']);

            $length = '100';
            if (isset($exprArgs[1])) {
                $length = $this->compiler->expression($exprArgs[1]['expr']);
            }

            $endString = '"..."';
            if (isset($exprArgs[2])) {
                $endString = $this->compiler->expression($exprArgs[2]['expr']);
            }

            return '(new \Vegas\Tag\ShortenText())->render('.$text.','.$length.', '.$endString.')';
        };
    }
}