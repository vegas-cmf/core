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
namespace Vegas\Mvc\Controller;

use Phalcon\Mvc\Controller;

/**
 * Class ControllerAbstract
 * @package Vegas\Mvc\Controller
 */
abstract class ControllerAbstract extends Controller
{
    /**
     * Controller initialization block
     */
    public function initialize()
    {
        /**
         * Attaches event fired before view render
         * It allows to use $this->view->partial inside of action method before action view is being rendered
         */
        $this->eventsManager->attach('view:beforeRenderView', function($event, $view, $engineViewPath) {
            if ($view instanceof \Vegas\Mvc\View) {
                $view->setControllerViewPath($this->router->getControllerName());
            }
        });
    }

    /**
     * Renders JSON response
     * Disables view
     *
     * @param $data
     * @return null|\Phalcon\Http\ResponseInterface
     */
    protected function jsonResponse($data = array())
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');

        return $this->response->setJsonContent($data);
    }

    /**
     * _
     * Alias for $this->i18n->_().
     * Returns translated text.
     * 
     * @param mixed $text 
     * @access protected
     * @return string
     */
    protected function _($text)
    {
        return $this->i18n->_($text);
    }

    /**
     * Throws exception with code 403
     *
     * @param string $message
     * @throws \Vegas\Exception
     */
    protected function throw403($message = '')
    {
        throw new \Vegas\Exception($message, 403);
    }

    /**
     * Throws exception with code 404
     *
     * @param string $message
     * @throws \Vegas\Exception
     */
    protected function throw404($message = '')
    {
        throw new \Vegas\Exception($message, 404);
    }

    /**
     * Throws exception with code 500
     *
     * @param string $message
     * @throws \Vegas\Exception
     */
    protected function throw500($message = '')
    {
        throw new \Vegas\Exception($message, 500);
    }
}
