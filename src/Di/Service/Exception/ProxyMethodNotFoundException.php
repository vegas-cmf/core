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

namespace Vegas\Di\Service\Exception;

use Vegas\Di\Service\Exception as VegasException;

/**
 * Class ProxyMethodNotFoundException
 * @package Vegas\Di\Service\Exception
 */
class ProxyMethodNotFoundException extends VegasException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Proxy method has not been found';
}
