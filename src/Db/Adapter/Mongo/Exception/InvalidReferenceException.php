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

namespace Vegas\Db\Adapter\Mongo\Exception;

/**
 * Class InvalidReferenceException
 * @package Vegas\Db\Adapter\Mongo\Exception
 */
class InvalidReferenceException extends \Vegas\Db\Exception
{
    protected $message = 'Object is not in valid database reference format';
} 