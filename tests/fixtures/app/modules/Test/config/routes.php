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

    'testfoo' => array(
        'route' => '/testfoo/front/:action/:params',
        'paths' => array(
            'module'    =>  'Test',
            'controller' => 'Frontend\Foo',
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

    'testbrokencrud' => array(
        'route' => '/test/brokencrud/:action/:params',
        'paths' => array(
            'module' => 'Test',
            'controller' => 'Backend\BrokenCrud',
            'action' => 1,
            'params' => 2
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
    ),

    'testshort' => array(
        'route' => '/test/short/namespace/:action/:params',
        'paths' => array(
            'module'    =>  'Test',
            'controller' => 'Fake',
            'action' => 1,
            'params' => 2
        ),

        'params' => array(
            'auth' => 'authUser'
        )
    ),

);