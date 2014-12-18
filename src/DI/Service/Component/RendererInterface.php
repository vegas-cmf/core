<?php
/**
 * This file is part of Vegas package
 * 
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\DI\Service\Component;
use Phalcon\Mvc\View;

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
     * @param View $view
     */
    public function __construct(View $view = null);

    /**
     * Sets module name
     *
     * @param $name
     * @return $this
     */
    public function setModuleName($name);

    /**
     * Sets name of component template
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