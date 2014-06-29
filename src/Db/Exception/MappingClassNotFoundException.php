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

use Vegas\Db\Exception as DbException;

/**
 * Class MappingClassNotFoundException
 * @package Vegas\Db\Exception
 */
class MappingClassNotFoundException extends DbException
{
    /**
     * @param string $mappingClassName
     */
    public function __construct($mappingClassName)
    {
        $this->message = sprintf($this->message, $mappingClassName);
    }

    protected $message = 'Mapping class \'%s\' was not found';
} 