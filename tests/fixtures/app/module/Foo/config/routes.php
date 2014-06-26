<?php
return array(
    'test' => array(
        'route' => '/test/front/:action/:params',
        'paths' => array(
            'module'    =>  'Test',
            'controller' => 'Frontend\Fake',
            'action' => 1,
            'params' => 2
        )
    ),

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