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
namespace Vegas\Mvc\Dispatcher;

use Phalcon\Dispatcher,
    Vegas\Exception,
    Vegas\Dispatcher\Exception\CannotHandleErrorException;

class ExceptionResolver implements \Phalcon\DI\InjectionAwareInterface
{
    use \Vegas\DI\InjectionAwareTrait;
    
    public function resolve(\Exception $exception)
    {
        $config = $this->di->get('config');
        
        if ($config->environment === \Vegas\Core::DEV_ENV) {
            $error = $this->prepareDevEnvException($exception);
        } else {
            $error = $this->prepareLiveEnvException($exception);
        }
        
        try {
            $view = $this->di->getShared('view');
            $view->setLayout('error');
            $view->disableLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
            $view->error = $error;
            
            $response = $this->di->getShared('response');
            $response->setStatusCode($error->getCode(), $error->getMessage());
        } catch (\Exception $ex) {
            throw new CannotHandleErrorException();
        }
        if (!$response->isSent()) {

            return $response->send();
        }
    }
    
    private function prepareLiveEnvException(\Exception $exception)
    {
        switch ($exception->getCode()) {
            case 403:
                return new Exception('Access forbidden.', 403);
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
            case 404:
                return new Exception('The page does not exist.', 404);
            case 0:
            case Dispatcher::EXCEPTION_NO_DI:
            case Dispatcher::EXCEPTION_INVALID_PARAMS:
            case Dispatcher::EXCEPTION_CYCLIC_ROUTING:
            case 500:
                return new Exception('Application error', 500); 
            default:
                return new Exception('Bad request.', 400);
       }
    }
    
    private function prepareDevEnvException(\Exception $exception)
    {
        switch ($exception->getCode()) {
            case 403:
                return new Exception($exception->getMessage(), 403);
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
            case 404:
                return new Exception($exception->getMessage(), 404);
            default:
                return new Exception($exception->getMessage(), 500); 
       }
    }
}
