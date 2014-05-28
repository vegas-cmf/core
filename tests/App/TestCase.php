<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
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
        $this->di = DI::getDefault();
        $modules = ModuleLoader::dump($this->di);

        $app = new Application($this->di);
        $app->registerModules($modules);

        require_once TESTS_ROOT_DIR . '/fixtures/app/Bootstrap.php';
        $config = require TESTS_ROOT_DIR . '/fixtures/app/config/config.php';

        $config = new \Phalcon\Config($config);
        $bootstrap = new \Bootstrap($config);
        $bootstrap->setup();

        $this->bootstrap = $bootstrap;
    }
}
