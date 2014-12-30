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
 
namespace Vegas\Mvc\View\Engine;

use Vegas\Mvc\View\Exception as ViewException;

/**
 * Class Exception
 * @package Vegas\Mvc\View\Engine
 */
class Exception extends ViewException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'View engine exception';
}
