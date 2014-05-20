<?php
/**
 *
 *
 * @copyright Copyright (c) 2012 AUBI-plus GmbH (http://www.aubi-plus.de)
 * @version 28.01.2013 - 08:37:34
 * @author Marco Rieger
 */
namespace Auth\Factory;

use Auth\Service\Authorize;

use Auth\Acl\Acl;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

class AuthorizeFactory implements FactoryInterface {

	private $service = null;
	
	/**
	 * Einsteigspunkt Factory
	 *
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	 * @version 28.01.2013 - 08:45:28
	 * @author Marco Rieger
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
		
		if( $this->service ) {
			return $this->service;
		}
		
		$acl = new Acl( $serviceLocator );
		$this->service = $acl;
		
        return $acl;
	}
	
	
	
}