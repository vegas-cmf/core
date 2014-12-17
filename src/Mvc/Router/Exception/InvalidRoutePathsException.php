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

namespace Vegas\Mvc\Router\Exception;

use Vegas\Mvc\Router\Exception as RouterException;

/**
 * Class InvalidRouteTypeException
 * Thrown when trying to add route with empty path
 *
 * @package Vegas\Mvc\Router\Exception
 */
class InvalidRoutePathsException extends RouterException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Invalid route paths';
}