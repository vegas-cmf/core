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
     * @var
     */
    private $controllerViewPath;

    /**
     * Constructor
     * Prepares view settings and engine
     *
     * @param null $options
     * @param null $viewDir
     */
    public function __construct($options = null, $viewDir = null) {
        parent::__construct($options);

        if (isset($options['layoutsDir']) && $viewDir) {
            $this->setLayoutsDir($this->prepareRelativeLayoutsPath($options, $viewDir));
        }

        if (!empty($options['layout'])) {
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

                return $volt;
            },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
        ));
    }

    /**
     * Renders view for controller action
     *
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
     */
    private function prepareControllerViewPath($controllerName)
    {
        return str_replace('\\','/',strtolower($controllerName));
    }

    /**
     * Prepares relative layout path
     *
     * @param array $options
     * @param null $viewDir
     * @return string
     */
    private function prepareRelativeLayoutsPath(array $options, $viewDir = null)
    {
        $path = str_replace(APP_ROOT, '', realpath($options['layoutsDir']));

        $nbOfDirs = count(explode('/', $path));

        $baseDepth = '';
        for ($i=0; $i<$nbOfDirs; $i++) {
            $baseDepth .= ($i ? '/' : '').'..';
        }

        if ($viewDir) {
            $modPath = str_replace(APP_ROOT, '', realpath(dirname($viewDir)));
            $nbOfDirs = count(explode('/', $modPath)) - $nbOfDirs;

            for ($i=0; $i<$nbOfDirs; $i++) {
                $path = '/..'.$path;
            }
        }

        return $baseDepth.$path;
    }
}
