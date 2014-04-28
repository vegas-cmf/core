<?php
if (!defined('APP_ROOT')) define('APP_ROOT', dirname(dirname(__DIR__)));

return array(
    'application' => array(
        'servicesDir'   =>  APP_ROOT . '/app/services/',
        'configDir'     => dirname(__FILE__) . DIRECTORY_SEPARATOR,
        'libraryDir'     => dirname(APP_ROOT) . DIRECTORY_SEPARATOR,
        'pluginDir'      => APP_ROOT . '/app/plugins/',
        'cacheDir'       => APP_ROOT . '/cache/',
        'moduleDir'      => APP_ROOT . '/app/module/',
        'baseUri'        => '/',
        'language'       => 'nl_NL',
        'subModules'    =>  array(
            'frontend', 'backend', 'custom'
        )
    ),

    'plugins' => array(),

    'environment'    => 'development',

    'mongo' => array(
        'db' => 'vegas_test',
    ),
);