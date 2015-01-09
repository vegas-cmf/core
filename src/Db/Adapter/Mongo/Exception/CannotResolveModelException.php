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

namespace Vegas\Db\Adapter\Mongo\Exception;

/**
 * Class CannotResolveModelException
 * @package Vegas\Db\Adapter\Mongo\Exception
 */
class CannotResolveModelException extends \Vegas\Db\Exception
{
    protected $message = 'Cannot resolve model for \'%s\' collection';

    /**
     * @param string $collectionName
     */
    public function __construct($collectionName)
    {
        $this->message = sprintf($this->message, $collectionName);
    }
}
