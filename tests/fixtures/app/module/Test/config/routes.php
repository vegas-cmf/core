<?php
return array(
    'testfake' => array(
        'route' => '/test/fake/test',
        'paths' => array(
            'module'    =>  'Test',
            'controller' => 'Backend\Fake',
            'action' => 'test'
        ),

        'params' => array(
            'auth' => 'authUser'
        )
    ),

    'testcrud' => array(
        'route' => '/test/crud/:action/:params',
        'paths' => array(
            'module' => 'Test',
            'controller' => 'Backend\Crud',
            'action' => 1,
            'params' => 2
        )
    )
);