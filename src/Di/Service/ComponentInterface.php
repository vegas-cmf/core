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
namespace Vegas\Di\Service;

/**
 * Interface ComponentInterface
 * @package Vegas\Di\Service
 */
interface ComponentInterface
{
    /**
     * Constructor
     *
     * @param Component\RendererInterface $renderer
     */
    public function __construct(Component\RendererInterface $renderer = null);

    /**
     * Sets renderer object and setups module and template name
     *
     * @param Component\RendererInterface $renderer
     * @return $this|mixed
     */
    public function setRenderer(Component\RendererInterface $renderer);

    /**
     * Returns renderer instance
     *
     * @return mixed
     */
    public function getRenderer();

    /**
     * Renders component content using provided renderer or default renderer
     *
     * @param array $params
     * @return mixed
     */
    public function render($params = array());
}
