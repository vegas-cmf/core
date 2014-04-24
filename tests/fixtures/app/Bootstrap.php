<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Tests\fixtures\app;

use Vegas\Db\Mapping\Json;
use Vegas\Db\MappingManager;

class Bootstrap extends \Vegas\Application\Bootstrap
{
    public function setup()
    {
        parent::setup();
        $this->initDbMappings();
    }

    protected function initDbMappings()
    {
        $mappingManager = new MappingManager();
        $mappingManager->add(new Json());
    }
} 