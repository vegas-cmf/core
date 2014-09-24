<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\App;

use Phalcon\DI;
use Vegas\Mvc\Application;
use Vegas\Mvc\Controller\Crud;
use Vegas\Mvc\Module\ModuleLoader;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $bootstrap;
    protected $di;

    public function setUp()
    {
        $_SERVER['HTTP_HOST'] = 'vegas.dev';
        $_SERVER['REQUEST_URI'] = '/';

        $this->di = DI::getDefault();
        $modules = ModuleLoader::dump($this->di);

        $app = new Application();
        $app->registerModules($modules);

        require_once TESTS_ROOT_DIR . '/fixtures/app/Bootstrap.php';
        $config = require TESTS_ROOT_DIR . '/fixtures/app/config/config.php';

        $config = new \Phalcon\Config($config);
        $bootstrap = new \Bootstrap($config);
        $bootstrap->setup();

        $this->bootstrap = $bootstrap;
    }
}
