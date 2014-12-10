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

namespace Vegas\Test;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Bootstrap
     */
    protected $bootstrap;
    protected $di;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces([
            'Tests'  =>  APP_ROOT  . '/tests',
        ], true);

        $loader->registerDirs([APP_ROOT. '/app'], true);
        $loader->register();

        $config = require APP_ROOT . '/tests/config.php';

        $bootstrap = new \Bootstrap(new \Phalcon\Config($config));

        $this->di = $bootstrap->getDI();
        $this->di->set('request', function() {
            return new \Vegas\Test\Http\Request();
        }, true);

        $bootstrap->setup();
        $this->bootstrap = $bootstrap;

    }

    /**
     * Handles URI
     *
     * @param $uri
     * @return \Phalcon\Http\ResponseInterface
     */
    public function handle($uri)
    {
        $response = $this->bootstrap->getApplication()->handle($uri);
        return $response;
    }

    /**
     * @return \Vegas\Test\Http\Request
     */
    protected function request()
    {
        return $this->di->get('request');
    }

    /**
     * @return \Phalcon\DI\FactoryDefault|\Phalcon\DiInterface
     */
    public function getDI()
    {
        return $this->di;
    }
}
 