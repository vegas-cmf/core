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

    public function __construct($options = null) {
        parent::__construct($options);

        if (isset($options['layoutsDir'])) {
            $this->setLayoutsDir($options['layoutsDir']);
        }
        if (isset($options['layout'])) {
            $this->setLayout($options['layout']);
        }

        $this->registerEngines(array(
            '.volt' => function ($this, $di) use ($options) {
                    $volt = new \Vegas\Mvc\View\Engine\Volt($this, $di);
                    if (isset($options['cacheDir'])) {
                        $volt->setOptions(array(
                            'compiledPath' => $options['cacheDir'],
                            'compiledSeparator' => '_'
                        ));
                    }
                    
                    $volt->registerFilter('toString');
                    $volt->registerHelper('shortenText');
                    $volt->registerHelper('pagination');
                    
                    return $volt;
                },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
        ));
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
