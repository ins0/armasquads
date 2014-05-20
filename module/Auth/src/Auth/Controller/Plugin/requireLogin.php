<?php
namespace Auth\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class requireLogin extends AbstractPlugin
{
    protected $_authService;
    protected $_sm;

    public function setAuthService( $authService ){
        $this->_authService = $authService;
    }

    public function getAuthService() {
        return $this->_authService;
    }

    public function __invoke(){

        $authService = $this->getAuthService();

        $controllerClass = get_class($this->getController());
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));

        // admin
        if( strtolower( $moduleNamespace ) == 'administration' ) {
            $ns = 'admin';
        } else {
            $ns = 'frontend';
        }

        if( $authService->isLoggedIn() )
        {
            return true;
        }

        return $this->getAuthService()->redirect(
            $ns . '/login'
        );
    }

}
