<?php
return [
    'application' => [
        'environment'   =>  \Vegas\Constants::TEST_ENV,

        'serviceDir'   =>  APP_ROOT . '/app/services/',
        'configDir'     => APP_ROOT . '/app/config/',
        'libraryDir'     => APP_ROOT. '/lib/',
        'pluginDir'      => APP_ROOT . '/app/plugins/',
        'moduleDir'      => APP_ROOT . '/app/modules/',
        'taskDir'      => APP_ROOT . '/app/tasks/',
        'baseUri'        => '/',
        'language'       => 'nl_NL',
        'view'  => [
            'cacheDir'  =>  APP_ROOT . '/cache/',
            'layout'    =>  'main',
            'layoutsDir'    =>  APP_ROOT . '/app/layouts/',
            'partialsDir'    =>  APP_ROOT . '/app/layouts/partials/',
            'compileAlways' =>  true
        ]
    ],

    'plugins' => [
        'foo' => [
            'class' => 'Foo',
            'attach' => 'beforeDispatch'
        ]
    ],

    'mongo' => [
        'dbname'    => getenv('MONGO_DB_NAME'),
        'host'      => getenv('VEGAS_CMF_CORE_MONGO_PORT_27017_TCP_ADDR'),
        'port'      => getenv('VEGAS_CMF_CORE_MONGO_PORT_27017_TCP_PORT')
    ],

    'db'    =>  [
        "adapter" => 'mysql',
        'host' => getenv('VEGAS_CMF_CORE_MYSQL_PORT_3306_TCP_ADDR'),
        'port' => getenv('VEGAS_CMF_CORE_MYSQL_PORT_3306_TCP_PORT'),
        'dbname' => getenv('VEGAS_CMF_CORE_MYSQL_ENV_MYSQL_DATABASE'),
        'username' => getenv('VEGAS_CMF_CORE_MYSQL_ENV_MYSQL_USER'),
        'password' => getenv('VEGAS_CMF_CORE_MYSQL_ENV_MYSQL_PASSWORD'),
        'options' => [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ]
    ]
];