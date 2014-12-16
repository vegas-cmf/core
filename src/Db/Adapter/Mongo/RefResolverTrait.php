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
use Vegas\Db\Adapter\Mongo\Exception\InvalidReferenceException;

/**
 * Class RefResolverTrait
 * @package Vegas\Db\Adapter\Mongo
 */
trait RefResolverTrait
{
    /**
     * Returns corresponding object indicated by MongoDBRef
     *
     * @param $fieldName
     * @return mixed
     * @throws InvalidReferenceException
     */
    public function readRef($fieldName)
    {
        $oRef = $this->readNestedAttribute($fieldName);
        if (!\MongoDBRef::isRef($oRef)) {
            throw new InvalidReferenceException();
        }
        if (isset($this->dbRefs) && isset($this->dbRefs[$fieldName])) {
            $modelInstance = $this->instantiateModel($this->dbRefs[$fieldName]);
        } else if ($this->getDI()->has('mongoMapper')) {
            $modelInstance = $this->getDI()->get('mongoMapper')->resolveModel($oRef['$ref']);
        } else {
            return $oRef;
        }
        return forward_static_call(array($modelInstance, 'findById'), $oRef['$id']);
    }

    /**
     * Creates an instance from given model class name
     *
     * @param $modelName
     * @return object
     */
    protected function instantiateModel($modelName)
    {
        $reflectionClass = new \ReflectionClass($modelName);
        return $reflectionClass->newInstance();
    }

    /**
     * Returns dependency injector
     *
     * @return \Phalcon\DiInterface
     */
    abstract public function getDI();

    /**
     * Reads attribute value by its name
     *
     * @param $attribute
     * @return mixed
     */
    abstract public function readAttribute($attribute);

    /**
     * Reads nested object
     *
     * @param $attribute
     * @return mixed
     */
    abstract public function readNestedAttribute($attribute);
} 