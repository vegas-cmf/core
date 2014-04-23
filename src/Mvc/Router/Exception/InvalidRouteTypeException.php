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

namespace Vegas\Mvc\Router\Exception;

use Vegas\Mvc\Router\Exception as RouterException;

/**
 * Class InvalidRouteTypeException
 * Thrown when trying to add non existing route type.
 *
 * @package Vegas\Mvc\Router\Exception
 */
class InvalidRouteTypeException extends RouterException
{
    protected $message = 'Invalid route type';
}