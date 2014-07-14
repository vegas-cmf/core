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
namespace Vegas\DI\Service\Component;

/**
 * Interface RendererInterface
 * @package Vegas\DI\Service\Component
 */
interface RendererInterface
{
    /**
     * Constructor
     * Sets view instance
     *
     * @param \Phalcon\Mvc\View $view
     */
    public function __construct(\Phalcon\Mvc\View $view = null);

    /**
     * Sets name of template
     *
     * @param $name
     * @return $this
     */
    public function setModuleName($name);

    /**
     * Sets name of module
     *
     * @param $name
     * @return $this
     */
    public function setTemplateName($name);

    /**
     * Renders component's content from view
     *
     * @param array $params
     * @return string|void
     */
    public function render($params = array());
}