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
 
namespace Vegas\Db\Exception;

use Vegas\Db\Exception as DbException;

/**
 * Class InvalidMappingClassException
 * @package Vegas\Db\Exception
 */
class InvalidMappingClassException extends DbException
{
    /**
     * Exception default message
     *
     * @var string
     */
    protected $message = 'Mapping class is invalid';
}
