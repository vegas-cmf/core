<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl> Sławomir Żytko <slawek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc;

use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Exception;
use Vegas\Mvc\View\Engine\Volt;

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

        if (isset($options['partialsDir']) && $options['partialsDir']) {
            $this->setPartialsDir($options['partialsDir']);
        }

        if (!$this->getPartialsDir() && $viewDir) {
            $this->setPartialsDir($viewDir);
        }

        if (isset($options['layout']) && !empty($options['layout'])) {
            $this->setLayout($options['layout']);
        }

        $this->registerEngines(array(
            '.volt' => function ($this, $di) use ($options) {
                    $volt = new Volt($this, $di);
                    if (isset($options['cacheDir'])) {
                        $volt->setOptions(array(
                            'compiledPath' => $options['cacheDir'],
                            'compiledSeparator' => '_',
                            'compileAlways' => isset($options['compileAlways']) ? $options['compileAlways'] : false
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
     * Full path to controller view
     *
     * @var bool
     */
    private $controllerFullViewPath = false;

    /**
     * Checks whether view exists on registered extensions and render it
     *
     * @override
     * @param array $engines
     * @param string $viewPath
     * @param boolean $silence
     * @param boolean $mustClean
     * @param \Phalcon\Cache\BackendInterface $cache
     * @return null|void
     * @throws Exception
     */
    protected function _engineRender($engines, $viewPath, $silence, $mustClean, $cache)
    {
        $basePath = $this->_basePath;
        $notExists = true;

        if (is_object($cache)) {
            $renderLevel = intval($this->_renderLevel);
            $cacheLevel = intval($this->_cacheLevel);
            if ($renderLevel >= $cacheLevel) {
                if ($cache->isStarted() == false) {
                    $viewOptions = $this->_options;
                    if (is_array($viewOptions)) {
                        if (isset($viewOptions['cache'])) {
                            $cacheOptions = $viewOptions['cache'];
                            if (is_array($cacheOptions)) {
                                if (isset($cacheOptions['key'])) {
                                    $key = $cacheOptions['key'];
                                }
                                if (isset($cacheOptions['lifetime'])) {
                                    $lifeTime = $cacheOptions['lifetime'];
                                }
                            }

                            if (!isset($key) || !$key) {
                                $key = md5($viewPath);
                            }

                            if (!isset($lifeTime)) {
                                $lifeTime = 0;
                            }

                            $cachedView = $cache->start($key, $lifeTime);
                            if (!$cachedView) {
                                $this->_content = $cachedView;
                                return null;
                            }
                        }

                        if (!$cache->isFresh()) {
                            return null;
                        }
                    }
                }
            }
        }
        $viewParams = $this->_viewParams;
        $eventsManager = $this->_eventsManager;

        foreach ($engines as $extension => $engine) {
            $viewEnginePath = $basePath . $this->resolveFullViewPath($viewPath) . $extension;

            if (file_exists($viewEnginePath)) {
                if (is_object($eventsManager)) {
                    $this->_activeRenderPath = $viewEnginePath;
                    if ($eventsManager->fire("view:beforeRenderView", $this, $viewEnginePath) === false) {
                        continue;
                    }
                }
                $engine->render($viewEnginePath, $viewParams, $mustClean);

                $notExists = false;
                if (is_object($eventsManager)) {
                    $eventsManager->fire("view:afterRenderView", $this);
                }
                break;
            }
        }
        if ($notExists) {
            if (is_object($eventsManager)) {
                $this->_activeRenderPath = $viewEnginePath;
                $eventsManager->fire("view:notFoundView", $this, $viewEnginePath);
            }

            if (!$silence) {
                throw new Exception(sprintf("View %s was not found in the views directory", $viewEnginePath));
            }
        }
    }

    /**
     * Resolves full path to view file
     *
     * @param $viewPath
     * @return string
     */
    private function resolveFullViewPath($viewPath)
    {
        if (strlen($this->getPartialsDir()) > 0 && strpos($viewPath, $this->getPartialsDir()) === 0) {
            return $this->resolvePartialPath($viewPath);
        }
        if (strpos($viewPath, $this->getLayoutsDir()) === 0) {
            return $this->resolveLayoutPath($viewPath);
        }

        return $this->resolveViewPath($viewPath);
    }

    /**
     * Resolves view path
     *
     * @param $viewPath
     * @return string
     */
    private function resolveViewPath($viewPath)
    {
        $path = realpath($this->_viewsDir . dirname($viewPath)) . DIRECTORY_SEPARATOR . basename($viewPath);
        return $path;
    }

    /**
     * Resolves path to partial
     *
     * application->view->partialsDir option is optional
     * When partialsDir is not set in configuration, then by default partialsDir is the same like current viewsDir
     * Otherwise, when partialsDir is set to for example directory app/layouts/partials then partial function loads
     * global partials from this directory
     *
     * <code>
     *      //remember about trailing slashes
     *      'application' => array(
     *      ...
     *          'view' => array(
     *              'layout' => 'main',
     *              'layoutsDir' => APP_ROOT . '/app/layouts/',
     *              'partialsDir' => APP_ROOT . '/app/layouts/partials/', //[optional]
     *              ...
     *          )
     *      )
     *      ...
     * </code>
     * Usage:
     *  -   Relative partial
     *      <code>
     *          {# somewhere in module view #}
     *          {{ partial('../../../layouts/partials/header/navigation') }
     *          # goes to APP_ROOT/app/layouts/partials/header/navigation.volt
     *      </code>
     *      <code>
     *          {# somewhere in module view eg. Test/views/index/index.volt #}
     *          {{ partial('./frontend/foo/partials/other.volt') }
     *          # goes to APP_ROOT/app/modules/Test/views/frontend/foo/partials/other.volt
     *      </code>
     *
     *  -   Global partial
     *      <code>
     *          {{ partial('header/navigation') }}
     *          # when partialsDir is set to APP_ROOT/app/layouts/partials
     *          # it goes to APP_ROOT/app/layouts/partials/header/navigation.volt
     *          # otherwise it is looking for view inside current viewsDir, so: APP_ROOT/app/modules/Test/views/header/navigation.volt
     *      </code>
     *
     * -    Local partial in module Test, controller Index (app/modules/Test/views/index/)
     *      <code>
     *          {{ partial('./frontend/index/partials/content/heading') }}
     *          # goes to APP_ROOT/app/modules/Test/views/frontend/index/partials/content/heading.volt
     *      </code>
     *
     * -    Absolute path
     *      <code>
     *          {{ partial(constant("APP_ROOT") ~ "/app/layouts/partials/header/navigation.volt") }}
     *      </code>
     *
     * @param $viewPath
     * @return string
     */
    private function resolvePartialPath($viewPath)
    {
        $tempViewPath = str_replace($this->getPartialsDir(), '', $viewPath);

        if (strpos($tempViewPath, '../') === 0 || strpos($tempViewPath, '/../') === 0) {
            return $this->resolveRelativePath($tempViewPath);
        } else if (strpos($tempViewPath, './') === 0) {
            return $this->resolveLocalPath($tempViewPath);
        } else if (file_exists(dirname($tempViewPath))) {
            return $tempViewPath;
        }

        return $this->resolveGlobalPath($tempViewPath);
    }

    /**
     * Resolves path to layouts directory
     *
     * @param $viewPath
     * @return string
     */
    private function resolveLayoutPath($viewPath)
    {
        return $viewPath;
    }

    /**
     * Resolves path to local partials directory
     *
     * @param $partialPath
     * @return string
     */
    private function resolveLocalPath($partialPath)
    {
        $partialDir = str_replace('./', '', dirname($partialPath));
        $partialsDir = realpath(sprintf('%s%s',
                $this->_viewsDir,
                $partialDir
            )) . DIRECTORY_SEPARATOR;

        return $partialsDir . basename($partialPath);
    }

    /**
     * Resolves path to global partials directory
     *
     * @param $partialPath
     * @return string
     */
    private function resolveGlobalPath($partialPath)
    {
        $partialsDirPath = $this->getPartialsDir();
        return $partialsDirPath . $partialPath;
    }

    /**
     * Resolves `realpath` from relative partial path
     *
     * @param $partialPath
     * @return string
     */
    private function resolveRelativePath($partialPath)
    {
        $partialsDirPath = realpath(sprintf('%s%s',
                $this->_viewsDir,
                dirname($partialPath)
            )) . DIRECTORY_SEPARATOR;

        return $partialsDirPath . basename($partialPath);
    }

    /**
     * Renders view for controller action
     *
     * @override
     * @param string $controllerName
     * @param string $actionName
     * @param null $params
     * @return PhalconView|void
     */
    public function render($controllerName, $actionName, $params = null) {
        if (empty($this->controllerViewPath)) {
            $this->setControllerViewPath($controllerName);
        }
        parent::render($this->controllerViewPath, $actionName, $params);
    }

    /**
     * Returns controller's view path
     *
     * @return string
     */
    public function getControllerViewPath()
    {
        return $this->controllerViewPath;
    }

    /**
     * Prepares and sets path for controller view
     *
     * @param $controllerName
     * @return mixed
     * @internal
     */
    public function setControllerViewPath($controllerName)
    {
        $this->controllerViewPath = str_replace('\\','/',strtolower($controllerName));
        $this->controllerFullViewPath = $this->_viewsDir . $this->controllerViewPath;
    }
}
