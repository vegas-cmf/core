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
namespace Vegas\Mvc\Controller;

use Vegas\Exception as VegasException;

/**
 * Class Exception
 * @package Vegas\Mvc\Controller
 */
class Exception extends VegasException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $_message = 'Unknown MVC Controller exception';
}
 