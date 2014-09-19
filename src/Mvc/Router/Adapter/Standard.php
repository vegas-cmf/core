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
 
namespace Vegas\Mvc\Router\Adapter;


use Phalcon\DI;
use Phalcon\DiInterface;
use Vegas\DI\InjectionAwareTrait;

/**
 * Class Standard
 * Default router using standard Phalcon router.
 *
 * @package Vegas\Mvc\Router\Adapter
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Router.html
 */
class Standard extends \Phalcon\Mvc\Router implements DI\InjectionAwareInterface
{

    /**
     * Standard router constructor
     *
     * @param DiInterface $dependencyInjector
     * @param bool $keepDefaultRoutes
     */
    public function __construct(\Phalcon\DiInterface $dependencyInjector, $keepDefaultRoutes = false)
    {
        parent::__construct($keepDefaultRoutes);
        $this->removeExtraSlashes(true);
        $this->setDI($dependencyInjector);
    }
}