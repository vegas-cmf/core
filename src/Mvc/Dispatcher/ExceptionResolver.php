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
namespace Vegas\Mvc\Dispatcher;

use Phalcon\Dispatcher;
use Vegas\Constants;
use Vegas\Exception;
use Vegas\Mvc\Dispatcher\Exception\CannotHandleErrorException;
use Vegas\Exception as VegasException;
use Vegas\Mvc\View;

/**
 * Class ExceptionResolver
 * @package Vegas\Mvc\Dispatcher
 */
class ExceptionResolver implements \Phalcon\DI\InjectionAwareInterface
{
    use \Vegas\DI\InjectionAwareTrait;

    /**
     * Resolves application error and renders error
     *
     * @param \Exception $exception
     * @throws \Vegas\Mvc\Dispatcher\Exception\CannotHandleErrorException
     * @return object
     */
    public function resolve(\Exception $exception)
    {
        if (Constants::DEFAULT_ENV === $this->di->get('environment')) {
            $error = $this->prepareLiveEnvException($exception);
        } else {
            $error = $this->prepareDevEnvException($exception);
        }

        try {
            $rendered = $this->renderLayoutForError($error);

            $response = $this->di->getShared('response');
            $response->setStatusCode($error->getCode(), $error->getMessage());
        } catch (\Exception $ex) {
            throw new CannotHandleErrorException($ex->getMessage());
        }

        if (!$response->isSent()) {
            if (!$rendered) {
                $this->displayRawError($error);
            }

            return $response->send();
        }

        return $response;
    }

    /**
     * Prepares exception for live environment
     *
     * @param \Exception $exception
     * @return VegasException
     * @internal
     */
    private function prepareLiveEnvException(\Exception $exception)
    {
        switch ($exception->getCode()) {
            case 403:
                return new VegasException('Access forbidden.', 403, $exception);
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
            case 404:
                return new VegasException('The page does not exist.', 404, $exception);
            case 0:
            case Dispatcher::EXCEPTION_NO_DI:
            case Dispatcher::EXCEPTION_INVALID_PARAMS:
            case Dispatcher::EXCEPTION_CYCLIC_ROUTING:
            case 500:
                return new VegasException('Application error.', 500, $exception);
            default:
                return new VegasException('Bad request.', 400, $exception);
        }
    }

    /**
     * Prepares error for development environment
     *
     * @param \Exception $exception
     * @return VegasException
     * @internal
     */
    private function prepareDevEnvException(\Exception $exception)
    {
        switch ($exception->getCode()) {
            case 403:
                return new VegasException($exception->getMessage(), 403, $exception);
            case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
            case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
            case 404:
                return new VegasException($exception->getMessage(), 404, $exception);
            default:
                return new VegasException($exception->getMessage(), 500, $exception);
        }
    }

    /**
     * Renders error using error layout
     *
     * @param VegasException $error
     * @return bool
     * @internal
     */
    private function renderLayoutForError(VegasException $error)
    {
        $view = $this->di->getShared('view');
        $config = $this->di->get('config');

        $engines = $view->getRegisteredEngines();

        if (empty($config->application->view->layoutsDir)) {
            return false;
        }

        foreach ($engines As $ext => $engine) {
            if (file_exists($config->application->view->layoutsDir.'error'.$ext)) {
                $view->setLayout('error');
                $view->disableLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
                $view->error = $error;

                return true;
            }
        }

        return false;
    }

    /**
     * Displays raw error without using any layout
     *
     * @param Exception $error
     * @internal
     */
    private function displayRawError(VegasException $error)
    {
        if (Constants::DEV_ENV === $this->di->get('environment')) {
            trigger_error(
                $error->getCode() . ' ' .
                $error->getMessage() . ' ' .
                $error->getPrevious()->getTraceAsString(),
                E_USER_ERROR
            );
        } elseif (Constants::TEST_ENV === $this->di->get('environment')) {
            echo $error->getCode(). PHP_EOL .$error->getMessage() . PHP_EOL . $error->getPrevious()->getTraceAsString();
        }
    }
}