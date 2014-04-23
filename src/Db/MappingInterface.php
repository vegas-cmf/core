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
    public function getName();

    public function resolve($value);
} 