<?php
/**
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * Date: 4/24/14
 * Time: 1:24 PM
 */

//Test Suite bootstrap
include __DIR__ . "/../vendor/autoload.php";

define('TESTS_ROOT_DIR', dirname(__FILE__));

$configArray = require_once dirname(__FILE__) . '/fixtures/app/config/config.php';

$config = new \Phalcon\Config($configArray);
$di = new Phalcon\DI\FactoryDefault();

$di->set('config', $config);

$di->set('mongo', function() use ($config) {
    $mongo = new \MongoClient();
    return $mongo->selectDb($config->mongo->db);
}, true);

Phalcon\DI::setDefault($di);