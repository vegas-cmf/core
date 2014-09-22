<?php
/**
 * This file is part of Vegas package
 *
 * @author Jaroslaw Macko <jarek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\DI\Service\Exception;

use Vegas\DI\Service\Exception as VegasException;

/**
 * Class MethodNotFoundException
 * @package Vegas\DI\Service\Exception
 */
class ModulesNotSetException extends VegasException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Modules are not set in DI container.';
}
 
