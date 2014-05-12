<?php
error_reporting(E_ALL);

define('APP_ROOT', dirname(dirname(__FILE__)));

try {
    require APP_ROOT . '/../../vendor/autoload.php';
    require APP_ROOT . '/cli/Bootstrap.php';
    $config = require APP_ROOT . '/app/config/config.php';

    $bootstrap = new \Bootstrap(new \Phalcon\Config($config));
    $bootstrap->setArguments($argv);

    $bootstrap->setup()->run();
    echo PHP_EOL;
} catch (\Exception $ex) {
    echo $ex->getMessage();
    echo PHP_EOL;
    echo $ex->getLine();
    echo PHP_EOL;
    echo $ex->getTraceAsString();
}