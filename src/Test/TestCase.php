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

use Phalcon\DI;
use Vegas\Test\Bootstrap;

/**
 * @codeCoverageIgnore
 */
class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Bootstrap
     */
    protected $bootstrap;

    /**
     * @var \Phalcon\DI\FactoryDefault|\Phalcon\DiInterface
     */
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
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $config = DI::getDefault()->get('config');
        $bootstrap = new Bootstrap($config);
        $bootstrap->setup();

        $this->di = $bootstrap->getDI();
        $this->bootstrap = $bootstrap;

        DI::setDefault($bootstrap->getDI());
    }

    /**
     * Handles URI
     *
     * @param $uri
     * @return \Phalcon\Http\ResponseInterface
     */
    public function handleUri($uri)
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

    /**
     * @return \Bootstrap|Bootstrap
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }
}
