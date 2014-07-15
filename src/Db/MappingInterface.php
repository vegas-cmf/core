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


namespace Vegas\Db;

/**
 * Interface MappingInterface
 * @package Vegas\Db
 */
interface MappingInterface 
{
    /**
     * Returns the name of mapper using in MappingManager
     *
     * @return mixed
     */
    public function getName();

    /**
     * Applies mappings for indicated value
     * Note that value is passed by reference
     *
     * @param $value
     * @return mixed
     */
    public function resolve(& $value);
} 