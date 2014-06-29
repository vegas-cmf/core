<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Db;

use Vegas\Exception as VegasException;

/**
 * Class Exception
 * @package Vegas\Db
 */
class Exception extends VegasException
{
    protected $message = 'Database exception';
}
 