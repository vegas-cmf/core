<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Db\Exception;

/**
 * Class NoRequiredServiceException
 * @package Vegas\Db\Exception
 */
class NoRequiredServiceException extends \Vegas\Db\Exception
{
    /**
     * Exception default message
     * @var string
     */
    protected $message = 'Required service is not available';
} 