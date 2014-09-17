<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc;

use Phalcon\Mvc\View as PhalconView;

/**
 * Class View
 * @package Vegas\Mvc
 */
class View extends PhalconView
{
    /**
     * @var string
     * @internal
     */
    private $controllerViewPath;

    /**
     * Constructor
     * Prepares view settings and engine
     *
     * @override
     * @param null $options
     * @param null $viewDir
     */
    public function __construct($options = null, $viewDir = null) {
        parent::__construct($options);

        if (isset($options['layoutsDir'])) {
            $this->setLayoutsDir($options['layoutsDir']);
        }

        if (isset($options['partialsDir'])) {
            $this->setPartialsDir($options['partialsDir']);
        } else {
            $this->setPartialsDir($options['layoutsDir'] . 'partials/');
        }

        if (isset($options['layout']) && !empty($options['layout'])) {
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
                    $volt->registerFilters();
                    $volt->registerHelpers();
                    $volt->setExtension('.volt');

                    return $volt;
                },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
        ));
    }

    /**
     * Checks whether view exists on registered extensions and render it
     *
     * @override
     * @param array $engines
     * @param string $viewPath
     * @param boolean $silence
     * @param boolean $mustClean
     * @param \Phalcon\Cache\BackendInterface $cache
     */
    protected function _engineRender($engines, $viewPath, $silence, $mustClean, $cache)
    {
        //checks if layout template is rendered
        //get rid of trailing slash
        if (dirname($viewPath) == rtrim($this->getLayoutsDir(), DIRECTORY_SEPARATOR)) {
            //when layouts is rendered change viewsDir to layoutsDir path
            $this->setViewsDir($this->getLayoutsDir());
            $viewPath = basename($viewPath);
        }
        parent::_engineRender($engines, $viewPath, $silence, $mustClean, $cache);
    }

    /**
     * Renders view for controller action
     *
     * @oerride
     * @param string $controllerName
     * @param string $actionName
     * @param null $params
     * @return PhalconView|void
     */
    public function render($controllerName, $actionName, $params = null) {
        if (empty($this->controllerViewPath)) {
            $this->controllerViewPath = $this->prepareControllerViewPath($controllerName);
        }
        parent::render($this->controllerViewPath, $actionName, $params);
    }

    /**
     * Prepares path for controller view
     *
     * @param $controllerName
     * @return mixed
     * @internal
     */
    private function prepareControllerViewPath($controllerName)
    {
        if (strpos($controllerName, '\\')) {
            $controllerName = str_replace('\\','/',strtolower($controllerName));
        }

        return $controllerName;
    }
}
