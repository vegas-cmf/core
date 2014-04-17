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

namespace Vegas\Mvc;

use Phalcon\Mvc\View as PhalconView;

class View extends PhalconView
{
    private $controllerViewPath;
    private $compiler;
    
    public function __construct($options = null) {
        parent::__construct($options);
        
        $config = require APP_CONFIG.'/config.php';

        $this->setLayoutsDir('../../../layouts/');
        $this->setLayout('main');

        $this->registerEngines(array(
            '.volt' => function ($this, $di) use ($config) {
                    $volt = new PhalconView\Engine\Volt($this, $di);
                    $volt->setOptions(array(
                        'compiledPath' => $config->application->cacheDir,
                        'compiledSeparator' => '_'
                    ));
                    
                    $this->compiler = $volt->getCompiler();
                    $this->registerTagsAndFilters();
                    
                    return $volt;
                },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
        ));
    }

    private function registerTagsAndFilters()
    {
        $this->shortenTextFunction();
        $this->paginationFunction();
        $this->toStringFilter();
    }
    
    private function shortenTextFunction()
    {
        $this->compiler->addFunction('shortenText',
            function($resolvedArgs, $exprArgs) {
                $text = $this->compiler->expression($exprArgs[0]['expr']);

                $length = '100';
                if (isset($exprArgs[1])) {
                    $length = $this->compiler->expression($exprArgs[1]['expr']);
                }
                
                $endString = '"..."';
                if (isset($exprArgs[2])) {
                    $endString = $this->compiler->expression($exprArgs[2]['expr']);
                }

                return '\Vegas\Tag::shortenText('.$text.','.$length.', '.$endString.')';
            }
        );
    }

    private function paginationFunction()
    {
        $this->compiler->addFunction('pagination',
            function($resolvedArgs, $exprArgs) {
                $page = $this->compiler->expression($exprArgs[0]['expr']);
                $options = array();

                if (isset($exprArgs[1])) {
                    $options = $this->compiler->expression($exprArgs[1]['expr']);
                }
                
                $optionString = 'array(';
                foreach ($options As $key => $option) {
                    $optionString .= '"'.$key.'" => '.$option;
                }
                $optionString .= ')';
                
                return '\Vegas\Tag::pagination('.$page.','.$optionString.')';
            }
        );
    }
    
    private function toStringFilter() 
    {
        $this->compiler->addFilter('toString', function($resolvedArgs, $exprArgs) {
            return sprintf('(string)%s', $resolvedArgs);
        });
    }
    
    public function render($controllerName, $actionName, $params = null) {
        if (empty($this->controllerViewPath)) {
            $this->controllerViewPath = $this->prepareControllerViewPath($controllerName);
        }

        parent::render($this->controllerViewPath, $actionName, $params);
    }
    
    private function prepareControllerViewPath($controllerName)
    {
        return str_replace('\\','/',strtolower($controllerName));
    }
}
