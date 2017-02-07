<?php
/**
 * This file is part of Vegas package
 *
 * @author Jaroslaw Macko <jarek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Di\Service;

use Vegas\Di\Service\Exception\MethodNotFoundException;
use Vegas\Di\Service\Exception\ProxyMethodNotFoundException;

/**
 * Class ModelProxyAbstract
 *
 * By extending ModelProxyAbstract, class receives access to methods defined in $model object
 * Usage
 * <code>
 * class Test extends \Vegas\Di\Service\ModelProxyAbstract implements \Phalcon\Di\InjectionAwareInterface
 * {
 *      use \Vegas\Di\InjectionAwareTrait;
 *
 *      public function __construct()
 *      {
 *          $this->model = new \TestModule\Models\TestModel();
 *      }
 *      //...
 * }
 *
 * //...
 * $test = new Test();
 * $records = $test->find();
 * </code>
 *
 * @package Vegas\Di\Service
 */
abstract class ModelProxyAbstract 
{
    /**
     * Model object
     *
     * @var \stdClass
     */
    protected $model = null;

    /**
     * Magic method that calls method which does not exist in child-class
     * on the model object when it's initialized
     *
     * @param $method
     * @param $args
     * @return mixed
     * @throws Exception\MethodNotFoundException
     * @throws Exception\ProxyMethodNotFoundException
     */
    public function __call($method, $args)
    {
        if (!empty($this->model)) {
            if (method_exists($this->model, $method)) {
                return call_user_func_array(array($this->model, $method), $args);
            } else {
                throw new ProxyMethodNotFoundException("Method: {$method} not 
                    exists in model: ".get_class($this->model));
            }
        } else {
            throw new MethodNotFoundException("Method: {$method} not exists in 
                service: ".get_class($this));
        }
    }
}
