<?php
/**
 *
 *
 * @copyright Copyright (c) 2012 AUBI-plus GmbH (http://www.aubi-plus.de)
 * @version 28.01.2013 - 08:37:34
 * @author Marco Rieger
 */
namespace Auth\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class hasAccessFactory implements FactoryInterface {

	/**
	 * @var ServiceManager
	 */
	private $sm;

	private $service = null;
	
	/**
	 * Einsteigspunkt Factory
	 *
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	 * @version 28.01.2013 - 08:45:28
	 * @author Marco Rieger
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $pluginManager) {
		
		$accessPlugin = new \Auth\Controller\Plugin\hasAccess();
		$accessPlugin->setAuthService( $pluginManager->getServiceLocator()->get('AuthService') );
		
		return $accessPlugin;
	}
	
	
	
}