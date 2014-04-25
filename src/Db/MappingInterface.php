<?php
/**
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * Date: 4/23/14
 * Time: 2:01 PM
 */

namespace Vegas\Db;

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