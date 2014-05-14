    <?php
if (!defined('APP_ROOT')) define('APP_ROOT', dirname(dirname(__DIR__)));

return array(
    'application' => array(
        'environment'   =>  'production',

        'servicesDir'   =>  APP_ROOT . '/app/services/',
        'configDir'     => dirname(__FILE__) . DIRECTORY_SEPARATOR,
        'libraryDir'     => dirname(APP_ROOT) . DIRECTORY_SEPARATOR . '/lib/',
        'pluginDir'      => APP_ROOT . '/app/plugins/',
        'moduleDir'      => APP_ROOT . '/app/module/',
        'tasksDir'      => APP_ROOT . '/app/tasks/',
        'baseUri'        => '/',
        'language'       => 'nl_NL',
        'subModules'    =>  array(
            'frontend', 'backend', 'custom'
        ),
        'view'  => array(
            'cacheDir'  =>  APP_ROOT . '/cache/',
            'layout'    =>  'main.volt',
            'layoutsDir'    =>  APP_ROOT . '/app/layouts'
        )
    ),

    'plugins' => array(),

    'environment'    => 'development',

    'mongo' => array(
        'db' => 'vegas_test',
    ),
);