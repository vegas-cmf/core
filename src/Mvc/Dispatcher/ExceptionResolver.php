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

use Phalcon\Dispatcher;
use Vegas\Dispatcher\Exception\CannotHandleErrorException;
use Vegas\Exception as VegasException;

/**
 * Class ExceptionResolver
 * @package Vegas\Mvc\Dispatcher
 */
class ExceptionResolver implements \Phalcon\DI\InjectionAwareInterface
{
    use \Vegas\DI\InjectionAwareTrait;

    /**
     * @param \Exception $exception
     * @return mixed
     * @throws \Vegas\Dispatcher\Exception\CannotHandleErrorException
     */
    public function resolve(\Exception $exception)
    {
        $config = $this->di->get('config');
        
        if ($config->environment === $this->di->get('environment')) {
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

    /**
     * @param \Exception $exception
     * @return VegasException
     */
    private function prepareLiveEnvException(\Exception $exception)
    {
        switch ($exception->getCode()) {
            case 403:
                return new VegasException('Access forbidden.', 403);
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
            case 404:
                return new VegasException('The page does not exist.', 404);
            case 0:
            case Dispatcher::EXCEPTION_NO_DI:
            case Dispatcher::EXCEPTION_INVALID_PARAMS:
            case Dispatcher::EXCEPTION_CYCLIC_ROUTING:
            case 500:
                return new VegasException('Application error', 500);
            default:
                return new VegasException('Bad request.', 400);
       }
    }

    /**
     * @param \Exception $exception
     * @return VegasException
     */
    private function prepareDevEnvException(\Exception $exception)
    {
        switch ($exception->getCode()) {
            case 403:
                return new VegasException($exception->getMessage(), 403);
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
            case 404:
                return new VegasException($exception->getMessage(), 404);
            default:
                return new VegasException($exception->getMessage(), 500);
       }
    }
}
