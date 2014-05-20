<?php
namespace Auth\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class hasAccess extends AbstractPlugin
{
	protected $_authService;
	protected $_sm;
	
	public function setAuthService( $authService ){
		$this->_authService = $authService;
	}
	
	public function getAuthService() {
		return $this->_authService;
	}
	
	public function __invoke( $action ){
		
		return $this->getAuthService()->hasAccess( $action );
	}
	
}
