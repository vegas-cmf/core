<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vegas\Mvc\View\Engine;

use Vegas\Mvc\View\Engine\Volt\Exception\InvalidFilterException;
use Vegas\Mvc\View\Engine\Volt\Exception\UnknownFilterException;
use Vegas\Mvc\View\Engine\Volt\VoltFilterAbstract;
use Vegas\Mvc\View\Engine\Volt\VoltHelperAbstract;


/**
 * Class Volt
 * @package Vegas\Mvc\View\Engine
 */
class Volt extends \Phalcon\Mvc\View\Engine\Volt
{
    use RegisterFilters;
    use RegisterHelpers;

    /**
     * Extension of template file
     *
     * @var string
     */
    private $extension = '.volt';

    /**
     * Sets template file extension
     *
     * @param $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Registers a new filter in the compiler
     *
     * @param $filterName
     * @throws Volt\Exception\UnknownFilterException
     */
    public function registerFilter($filterName)
    {
        $className = __CLASS__ . '\\Filter\\' . ucfirst($filterName);
        try {
            $filterInstance = $this->getClassInstance($className);
            if (!$filterInstance instanceof VoltFilterAbstract) {
                throw new InvalidFilterException();
            }
            $this->getCompiler()->addFilter($filterName, $filterInstance->getFilter());
        } catch (\Exception $e) {
            throw new UnknownFilterException(sprintf('Filter \'%s\' does not exist', $filterName));
        }
    }

    /**
     * Registers a new helper in the compiler
     *
     * @param $helperName
     * @throws Volt\Exception\UnknownFilterException
     */
    public function registerHelper($helperName)
    {
        $className = __CLASS__ . '\\Helper\\' . ucfirst($helperName);
        try {
            $helperInstance = $this->getClassInstance($className);
            if (!$helperInstance instanceof VoltHelperAbstract) {
                throw new InvalidFilterException();
            }
            $this->getCompiler()->addFunction($helperName, $helperInstance->getHelper());
        } catch (\Exception $e) {
            throw new UnknownFilterException(sprintf('Helper \'%s\' does not exist', $helperName));
        }
    }

    /**
     * Creates an instance of indicated class name
     *
     * @param $className
     * @return object
     * @internal
     */
    private function getClassInstance($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->newInstance($this->getCompiler());
    }

    /**
     * Renders a partial inside another view
     *
     * Uses partialsDir from config
     * Methods check if partial is in local directory, if so then prepares full path to local file,
     * otherwise uses global partialsDir path
     *
     * Before use setup partialsDir in application config (app/config/config.php):
     * <code>
     *      //remember about trailing slashes
     *      'application' => array(
     *      ...
     *          'view' => array(
     *              'layout' => 'main',
     *              'layoutsDir' => APP_ROOT . '/app/layouts/',
     *              'partialsDir' => APP_ROOT . '/app/layouts/partials/',
     *              ...
     *          )
     *      )
     *      ...
     * </code>
     * Usage:
     *  -   Relative partial
     *      <code>
     *          {# somewhere in module view #}
     *          {{ partial('../../../layouts/partials/header/navigation') }    # goes to APP_ROOT/app/layouts/partials/header/navigation.volt
     *      </code>
     *
     *  -   Global partial
     *      <code>
     *          {{ partial('header/navigation') }} # goes to APP_ROOT/app/layouts/partials/header/navigation.volt
     *      </code>
     *
     * -    Local partial in module Test, controller Index (app/modules/Test/views/index/)
     *      <code>
     *          {{ partial('./content/heading') }} # goes to APP_ROOT/app/modules/Test/views/index/partials/content/heading.volt
     *      </code>
     *
     * -    Absolute path
     *      <code>
     *          {{ partial(constant("APP_ROOT") ~ "/app/layouts/partials/header/navigation.volt") }}
     *      </code>
     *
     * NOTE
     *  name of 'partial' directory inside of module must be the same as name of global 'partial' directory:
     *  APP_ROOT/app/layouts/partials   =>  ../Test/views/index/partials
     *
     * @param string $partialPath
     * @param null $params
     * @return string|void
     */
    public function partial($partialPath, $params = null)
    {
        if (strpos($partialPath, '../') === 0 || strpos($partialPath, '/../') === 0) {
            $this->renderRelativePartial($partialPath, $params);
        } else if (strpos($partialPath, './') === 0) {
            $this->renderLocalPartial($partialPath, $params);
        } else {
            $this->renderGlobalPartial($partialPath, $params);
        }
    }

    /**
     * Renders partial from local directory
     *
     * @param $partialPath
     * @param null $params
     */
    private function renderLocalPartial($partialPath, $params = null)
    {
        $partialsDirPath = sprintf('%s%s%s%s',
            dirname($this->view->getActiveRenderPath()),
            DIRECTORY_SEPARATOR,
            basename($this->view->getPartialsDir()),
            DIRECTORY_SEPARATOR
        );

        $this->renderPartial($partialsDirPath, $partialPath, $params);
    }

    /**
     * Renders partial from global directory
     *
     * @param $partialPath
     * @param null $params
     */
    private function renderGlobalPartial($partialPath, $params = null)
    {
        $partialsDirPath = $this->view->getPartialsDir();
        //allows absolute path
        if (file_exists($partialPath . $this->extension)) {
            $partialsDirPath = dirname($partialPath) . DIRECTORY_SEPARATOR;
            $partialPath = basename($partialPath);
            return $this->renderPartial($partialsDirPath, $partialPath, $params);
        }

        return $this->renderPartial($partialsDirPath, $partialPath, $params);
    }

    /**
     * Renders partial from relative path
     *
     * @param $partialPath
     * @param null $params
     */
    private function renderRelativePartial($partialPath, $params = null)
    {
        $partialsDirPath = realpath(sprintf('%s%s%s',
                dirname($this->view->getActiveRenderPath()),
                DIRECTORY_SEPARATOR,
                dirname($partialPath)
            )) . DIRECTORY_SEPARATOR;

        $partialPath = $partialsDirPath . basename($partialPath);

        $this->renderGlobalPartial($partialPath, $params);
    }

    /**
     * Renders partials using prepared path
     *
     * @param $viewPartialsDir
     * @param $partialPath
     * @param null $params
     */
    private function renderPartial($viewPartialsDir, $partialPath, $params = null)
    {
        $this->render($viewPartialsDir . $partialPath . $this->extension, $params);
    }
}