<?php
/**
 * This file is part of Vegas package
 *
 * @author Jaroslaw Macko <jarek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\DI\Service;

use Vegas\DI\Service\Exception\MethodNotFoundException;
use Vegas\DI\Service\Exception\ProxyMethodNotFoundException;

abstract class ModelProxyAbstract 
{
    protected $model = null;

    public function __call($method, $args) {
        if(!empty($this->model)) {
            if(method_exists($this->model, $method)) {
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
