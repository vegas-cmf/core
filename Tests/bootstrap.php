<?php
//Test Suite bootstrap
include __DIR__ . "/../vendor/autoload.php";

$config = require_once dirname(__FILE__) . '/config.php';

$di = new Phalcon\DI\FactoryDefault();

$di->set('mongo', function() use ($config) {
    $mongo = new \MongoClient();
    return $mongo->selectDb($config->mongo->db);
}, true);

Phalcon\DI::setDefault($di);