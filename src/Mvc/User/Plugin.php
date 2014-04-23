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

namespace Vegas\Mvc\User;

class Plugin extends \Phalcon\Mvc\User\Plugin
{
    const DEFAULT_BACKEND_AUTH = "authAdmin";
    const DEFAULT_FRONTEND_AUTH = "authUser";

    public function isOnBackend()
    {
        if (strpos($this->router->getControllerName(),\Vegas\Core::BACKEND_NAMESPACE) === 0) {
            return true;
        }
        
        return false;
    }
    
    public function isOnFrontend()
    {
        if (strpos($this->router->getControllerName(),\Vegas\Core::FRONTEND_NAMESPACE) === 0) {
            return true;
        }
        
        return false;
    }

    /**
     * @return bool|mixed
     */
    protected function ensureAuthenticationInCurrentScope()
    {
        if ($this->isOnFrontend()) {
            $auth = $this->getAuthenticationForScope(self::DEFAULT_FRONTEND_AUTH);
        } else {
            $auth = $this->getAuthenticationForScope(self::DEFAULT_BACKEND_AUTH);;
        }

        return $auth->isAuthenticated() ? $auth : false;
    }


    /**
     * @param $scope
     * @return bool|mixed
     */
    protected function getAuthenticationForScope($scope)
    {
        $auth = false;
        if ($this->di->has($scope)) {
            $auth = $this->di->get($scope);
        }

        return $auth;
    }
}
