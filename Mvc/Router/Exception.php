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
 
namespace Vegas\Mvc\Router;

use \Vegas\Exception as VegasException;

/**
 * Class Exception
 * @package Vegas\Mvc\Router
 */
class Exception extends VegasException
{
    protected $_message = 'Router unknown error';
}