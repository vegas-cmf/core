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
 

namespace Vegas\Db\Adapter\Mongo;


use Phalcon\DI;

class MapperTest extends \Vegas\Test\TestCase
{

    public function testShouldCreateMapAndSaveToFile()
    {
        $tmpFilePath = APP_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'mongo.map.php';
        $mapper = new Mapper(APP_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'modules');
        $map = $mapper->create($tmpFilePath);

        $this->assertFileExists($tmpFilePath);

        $this->assertInternalType('array', $map);
        $this->assertNotEmpty($map);

        $this->assertArrayHasKey('Child', $map);
        $this->assertTrue(in_array('\Ref\Models\Child', $map));

    }
} 