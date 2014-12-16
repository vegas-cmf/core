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

use Vegas\Db\Adapter\Mongo\Exception\CannotResolveModelException;
use Vegas\Util\FileWriter;

/**
 * Class Mapper
 * @package Vegas\Db\Adapter\Mongo
 */
class Mapper
{
    /**
     * Collections mapped to model classes
     * Array key is a name of collection
     * Array value is a full model class name
     * @var array
     */
    protected $map = [];

    /**
     * Container for already instantiated classes
     * @var array
     */
    protected $modelInstances = [];

    /**
     * @param string $inputDirectory        Path to directory containing model classes
     * @param string $modelClassPattern     Regex pattern to find model classes
     */
    public function __construct($inputDirectory, $modelClassPattern = '/(.*)models\/(.*)\.php/i')
    {
        $this->inputDirectory = $inputDirectory;
        $this->modelClassPattern = '/(.*)models\/(.*)\.php/i';
    }

    /**
     * Setups Mongo collections map
     * If cache file already exists, then map is read directly from this file
     *
     * @param string $cacheFilePath     Path to cache file. If null then cache is skipped
     * @return $this
     */
    public function create($cacheFilePath = null)
    {
        $map = [];
        if ($cacheFilePath) {
            $map = $this->resolveCachedMap($cacheFilePath);
        }
        if (!is_array($map)) {
            $map = $this->createMap();
            if ($cacheFilePath) {
                $this->cacheMap($cacheFilePath, $map);
            }
        }

        $this->map = $map;

        return $map;
    }

    /**
     * Resolves model class name for given collection name
     *
     * @param string $collectionName
     * @throws CannotResolveModelException
     * @return object
     */
    public function resolveModel($collectionName)
    {
        if (!array_key_exists($collectionName, $this->map)) {
            throw new CannotResolveModelException($collectionName);
        }
        $modelClassName = $this->map[$collectionName];
        try {
            if (!array_key_exists($modelClassName, $this->modelInstances)) {
                $reflectionClass = new \ReflectionClass($modelClassName);
                $modelInstance = $reflectionClass->newInstance();
                $this->modelInstances[$modelClassName] = $modelInstance;
            }
            return $this->modelInstances[$modelClassName];
        } catch (\Exception $e) {
            throw new CannotResolveModelException($collectionName);
        }
    }

    /**
     * Creates map by traversing input directory
     *
     * @return array
     */
    private function createMap()
    {
        $map = [];
        //browse directories recursively
        $directoryIterator = new \RecursiveDirectoryIterator($this->inputDirectory);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);
        foreach ($iterator as $file) {
            if ($file->getFileName() == '.' || $file->getFileName() == '..') {
                continue;
            }
            if (!preg_match($this->modelClassPattern, $file->getPathname())) {
                continue;
            }
            //resolves name of class
            $className = $this->resolveFileClassName($file);
            try {
                $classInstance = $this->instantiateClass($className);
                if ($classInstance instanceof \Phalcon\Mvc\Collection) {
                    $map[$classInstance->getSource()] = $className;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $map;
    }

    /**
     * Returns map from cache
     *
     * @param string $cacheFilePath     Path to cache file
     * @return bool|mixed
     */
    private function resolveCachedMap($cacheFilePath)
    {
        $cachedContent = false;
        if (file_exists($cacheFilePath)) {
            $cachedContent = require $cacheFilePath;
        }

        return $cachedContent;
    }

    /**
     * Saves generated map to file
     *
     * @param string $cacheFilePath     Path to cache file
     * @param array $map    Generated map
     * @return int
     */
    private function cacheMap($cacheFilePath, $map)
    {
        if (!file_exists(dirname($cacheFilePath))) {
            mkdir(dirname($cacheFilePath), 0777, true);
        }
        return FileWriter::writeObject($cacheFilePath, $map, true);
    }

    /**
     * Creates an instance from given class name
     *
     * @param string $className
     * @return object
     */
    protected function instantiateClass($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstance();
    }

    /**
     * Resolves name of class inside given file
     *
     * @param \SplFileInfo $fileInfo
     * @return string
     */
    protected function resolveFileClassName(\SplFileInfo $fileInfo)
    {
        $relativeFilePath = str_replace(
            $this->inputDirectory,
            '',
            $fileInfo->getPath()) . DIRECTORY_SEPARATOR .
                $fileInfo->getBasename('.' . $fileInfo->getExtension()
        );
        //converts file path to namespace
        //DIRECTORY_SEPARATOR will be converted to namespace separator => \
        //each directory name will be converted to first upper case
        $splitPath = explode(DIRECTORY_SEPARATOR, $relativeFilePath);
        $namespace = implode('\\', array_map(function($item) {
            return ucfirst($item);
        }, $splitPath));
        return $namespace;
    }

} 