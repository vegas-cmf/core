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

use Phalcon\Cache\BackendInterface;
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
     * Full path to controller view
     *
     * @var bool|string
     */
    private $controllerFullViewPath = false;

    /**
     * Constructor
     * Prepares view settings and engine
     *
     * @override
     * @param null $options
     * @param null $viewDir
     */
    public function __construct($options = null, $viewDir = null)
    {
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

        $view = $this;
        $view->registerEngines(array(
            '.volt' => function ($view, $di) use ($options) {
                    $volt = new Volt($view, $di);
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
     * Checks whether view exists on registered extensions and render it
     *
     * @override
     * @param array $engines
     * @param string $viewPath
     * @param boolean $silence
     * @param boolean $mustClean
     * @param \Phalcon\Cache\BackendInterface $cache
     * @throws Exception
     */
    protected function _engineRender($engines, $viewPath, $silence, $mustClean, \Phalcon\Cache\BackendInterface $cache = null)
	{
		$notExists = true;
		$basePath = $this->_basePath;
        $viewParams = $this->_viewParams;
		$eventsManager = $this->_eventsManager;
		$viewEnginePaths = [];

		foreach ($this->getViewsDirs() as $viewsDir) {

			if (!$this->_isAbsolutePath($viewPath)) {
				$viewsDirPath = $basePath . $this->resolveFullViewPath($viewsDir, $viewPath);
			} else {
                $viewsDirPath = $this->resolveFullViewPath($viewsDir, $viewPath);
			}

            if (is_object($cache)) {

                $renderLevel = (int) $this->_renderLevel;
				$cacheLevel = (int) $this->_cacheLevel;

				if ($renderLevel >= $cacheLevel) {

                    /**
                     * Check if the cache is started, the first time a cache is started we start the
                     * cache
                     */
                    if (!$cache->isStarted()) {

                        $key = null;
						$lifetime = null;

						$viewOptions = $this->_options;

						/**
                         * Check if the user has defined a different options to the default
                         */
						if ($cacheOptions = $viewOptions["cache"]) {
                            if (is_array($cacheOptions)) {
                                $key = $cacheOptions["key"];
                                $lifetime = $cacheOptions["lifetime"];
                            }
						}

						/**
                         * If a cache key is not set we create one using a md5
                         */
						if ($key === null) {
                            $key = md5($viewPath);
						}

						/**
                         * We start the cache using the key set
                         */
						$cachedView = $cache->start($key, $lifetime);
						if ($cachedView !== null) {
                            $this->_content = $cachedView;
							return null;
						}
					}

					/**
                     * This method only returns true if the cache has not expired
                     */
					if (!$cache->isFresh()) {
						return null;
					}
				}
			}

			/**
             * Views are rendered in each engine
             */
			foreach ($engines as $extension => $engine) {

                $viewEnginePath = $viewsDirPath . $extension;
				if (file_exists($viewEnginePath)) {

                    /**
                     * Call beforeRenderView if there is an events manager available
                     */
					if (is_object($eventsManager)) {
                        $this->_activeRenderPaths = [$viewEnginePath];
						if ($eventsManager->fire("view:beforeRenderView", $this, $viewEnginePath) === false) {
                            continue;
                        }
					}

					$engine->render($viewEnginePath, $viewParams, $mustClean);

					/**
                     * Call afterRenderView if there is an events manager available
                     */
//					$notExists = false;
					if (is_object($eventsManager)) {
                        $eventsManager->fire("view:afterRenderView", $this);
					}
					return;
				}

				$viewEnginePaths[] = $viewEnginePath;
			}
		}

		if ($notExists === true) {
            /**
             * Notify about not found views
             */
            if (is_object($eventsManager)) {
                $this->_activeRenderPaths = $viewEnginePaths;
				$eventsManager->fire("view:notFoundView", $this, $viewEnginePath);
			}

			if (!$silence) {
                throw new Exception(sprintf("View %s was not found in any of the views directory", $viewEnginePath));
            }
		}
	}

    /**
     * Resolves full path to view file
     *
     * @param $viewsDir
     * @param $viewPath
     * @return string
     */
    private function resolveFullViewPath($viewsDir, $viewPath)
    {
        if (strlen($this->getPartialsDir()) > 0 && strpos($viewPath, $this->getPartialsDir()) === 0) {
            return $this->resolvePartialPath($viewPath);
        }
        if (strpos($viewPath, $this->getLayoutsDir()) === 0) {
            return $this->resolveLayoutPath($viewPath);
        }

        return $this->resolveViewPath($viewsDir, $viewPath);
    }

    /**
     * Resolves view path
     *
     * @param $viewPath
     * @return string
     */
    private function resolveViewPath($viewsDir, $viewPath)
    {
        $path = realpath($viewsDir . dirname($viewPath)) . DIRECTORY_SEPARATOR . basename($viewPath);
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
        } else if (!in_array(dirname($tempViewPath), ['.','..'])
            && file_exists(dirname($tempViewPath))) {
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
        $partialsDir = implode(DIRECTORY_SEPARATOR, [
                rtrim($this->getViewsDir(), DIRECTORY_SEPARATOR),
                $partialDir,
                ''
        ]);

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
        $partialsDirPath = implode(DIRECTORY_SEPARATOR, [
            rtrim($this->getViewsDir(), DIRECTORY_SEPARATOR),
            dirname($partialPath),
            ''
        ]);

        return $partialsDirPath . basename($partialPath);
    }

    /**
     * Renders view for controller action
     *
     * @override
     * @param string $controllerName
     * @param string $actionName
     * @param array $params
     * @return bool|View
     */
    public function render($controllerName, $actionName, $params = null)
    {
        if (empty($this->controllerViewPath)) {
            $this->setControllerViewPath($controllerName);
        }
        return parent::render($this->controllerViewPath, $actionName, $params);
    }

    /**
     * @param string $partialPath
     * @param null $params
     * @return void
     */
    public function partial($partialPath, $params = null)
    {
        /**
         * Backwards compatibility for partial rendering without adding directory separator on left
         */
        if (strlen($this->getPartialsDir()) > 0
            && strpos($partialPath, DIRECTORY_SEPARATOR) !== 0
            && strrpos($this->getPartialsDir(), DIRECTORY_SEPARATOR) !== (strlen($this->getPartialsDir())-1)) {
            $partialPath = DIRECTORY_SEPARATOR . $partialPath;
        }
        parent::partial($partialPath, $params);
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
        $this->controllerViewPath = str_replace('\\', DIRECTORY_SEPARATOR , strtolower($controllerName));
        $this->controllerFullViewPath = $this->getViewsDir() . $this->controllerViewPath;
    }
}
