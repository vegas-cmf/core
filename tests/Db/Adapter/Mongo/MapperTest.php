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

namespace Vegas\Db\Adapter\Mongo;

use Phalcon\DI;

class MapperTest extends \Vegas\Test\TestCase
{
    private $tmpFilePath;

    private function getTmpFilePath()
    {
        return APP_ROOT . DIRECTORY_SEPARATOR . 'tmp'
                        . DIRECTORY_SEPARATOR . 'mongo_map'
                        . DIRECTORY_SEPARATOR . uniqid() . '.php';
    }

    public function setUp()
    {
        parent::setUp();

        $this->tmpFilePath = $this->getTmpFilePath();
        if (file_exists($this->tmpFilePath)) {
            unlink($this->tmpFilePath);
        }
        if (file_exists(dirname($this->tmpFilePath))) {
            rmdir(dirname($this->tmpFilePath));
        }
    }

    public function tearDown()
    {
        if (file_exists($this->tmpFilePath)) {
            unlink($this->tmpFilePath);
        }
    }

    public function testShouldCreateMapAndSaveToFile()
    {
        $mapper = new Mapper(APP_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'modules');
        $map = $mapper->create($this->tmpFilePath);

        $this->assertFileExists($this->tmpFilePath);

        $this->assertInternalType('array', $map);
        $this->assertNotEmpty($map);

        $this->assertArrayHasKey('Child', $map);
        $this->assertTrue(in_array('\Ref\Models\Child', $map));

        $cachedMap = $mapper->create($this->tmpFilePath);
        $this->assertSame($cachedMap, $map);
    }

    public function testShouldResolveModelFromMap()
    {
        $mapper = new Mapper(APP_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'modules');
        $map = $mapper->create($this->tmpFilePath);

        $model = $mapper->resolveModel(array_keys($map)[0]);
        $this->assertNotNull($model);
        $this->assertInstanceOf(array_values($map)[0], $model);
    }

    public function testShouldThrowExceptionForNotResolvedModel()
    {
        $mapper = new Mapper(APP_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'modules');
        $map = $mapper->create($this->tmpFilePath);

        $exception = null;
        try {
            $mapper->resolveModel('fakecollection');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Adapter\Mongo\Exception\CannotResolveModelException', $exception);

        //inject fake model class name
        $map['fakecollection'] = '\Fake\Models\NotExisting';
        $reflectionObject = new \ReflectionObject($mapper);
        $mapProperty = $reflectionObject->getProperty('map');
        $mapProperty->setAccessible(true);
        $mapProperty->setValue($mapper, $map);

        $exception = null;
        try {
            $mapper->resolveModel('fakecollection');
        } catch (\Exception $e) {
            $exception = $e;
        }
        $this->assertInstanceOf('\Vegas\Db\Adapter\Mongo\Exception\CannotResolveModelException', $exception);
    }
} 