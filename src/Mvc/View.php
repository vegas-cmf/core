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
     * @param null $options
     */
    public function __construct($options = null) {
        parent::__construct($options);

        if (!empty($options['view']['layoutsDir'])) {
            $this->setLayoutsDir($this->prepareRelativeLatoutsPath($options));
        }

        if (!empty($options['view']['layout'])) {
            $this->setLayout($options['view']['layout']);
        }

        $this->registerEngines(array(
            '.volt' => function ($this, $di) use ($options) {
                $volt = new \Vegas\Mvc\View\Engine\Volt($this, $di);
                if (!empty($options['view']['cacheDir'])) {
                    $volt->setOptions(array(
                        'compiledPath' => $options['view']['cacheDir'],
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
     * @param $controllerName
     * @return mixed
     */
    private function prepareControllerViewPath($controllerName)
    {
        return str_replace('\\','/',strtolower($controllerName));
    }

    /**
     * @param array $options
     * @return string
     */
    private function prepareRelativeLatoutsPath(array $options)
    {
        $path = str_replace(APP_ROOT, '', realpath($options['view']['layoutsDir']));

        $nbOfDirs = count(explode('/', $path));

        $baseDepth = '';
        for ($i=0; $i<$nbOfDirs; $i++) {
            $baseDepth .= ($i ? '/' : '').'..';
        }

        if (isset($options['moduleDir'])) {
            $modPath = str_replace(APP_ROOT, '', realpath($options['moduleDir']));
            $nbOfDirs = count(explode('/', $modPath));

            for ($i=0; $i<$nbOfDirs; $i++) {
                $path = '/..'.$path;
            }
        }

        return $baseDepth.$path;
    }
}
