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
 
namespace Vegas\Mvc\View\Engine\Volt\Exception;

use Vegas\Mvc\View\Engine\Exception;

/**
 * Class InvalidHelperException
 * @package Vegas\Mvc\View\Engine\Volt\Exception
 */
class InvalidHelperException extends Exception
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $_message = 'Invalid helper. Helper must be an instance of VoltHelperAbstract';
}
