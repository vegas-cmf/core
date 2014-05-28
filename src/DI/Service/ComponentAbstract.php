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
namespace Vegas\DI\Service;

use Phalcon\DI\InjectionAwareInterface;

abstract class ComponentAbstract implements ComponentInterface, InjectionAwareInterface
{
    use \Vegas\DI\InjectionAwareTrait;
    
    protected $renderer;
    protected $moduleName;
    protected $templateName;
    
    abstract protected function setUp($params = array());
    
    public function __construct(Component\RendererInterface $renderer = null)
    {
        if ($renderer) {
            $this->setRenderer($renderer);
        }
    }
    
    public function getRenderer()
    {
        if (empty($this->renderer)) {
            $this->setRenderer(new Component\Renderer($this->di->get('view')));
        }
        
        return $this->renderer;
    }
    
    public function setRenderer(Component\RendererInterface $renderer)
    {
        $nameArray = explode('\\',get_called_class());
        
        $moduleName = $nameArray[0];
        $templateName = lcfirst($nameArray[count($nameArray)-1]);

        $this->renderer = $renderer;
        $this->renderer->setModuleName($moduleName);
        $this->renderer->setTemplateName($templateName);
        
        return $this;
    }
    
    public function render($params = array())
    {
        $params = $this->setUp($params);
        
        return $this->getRenderer()->render($params);
    }

}