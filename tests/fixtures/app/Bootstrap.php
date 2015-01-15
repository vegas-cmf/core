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

use Vegas\Db\Mapping\Json;
use Vegas\Db\MappingManager;

class Bootstrap extends \Vegas\Mvc\Bootstrap
{
    public function setup()
    {
        parent::setup();
        $this->initDbMappings();

        return $this;
    }

    protected function initDbMappings()
    {
        $mappingManager = new MappingManager();
        $mappingManager->add(new Json());
    }
} 