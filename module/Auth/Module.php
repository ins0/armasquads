<?php
namespace Auth;

use Zend\Mvc\MvcEvent;

use Zend\EventManager\EventInterface;

class Module
{
    public function onBootstrap(EventInterface $e)
    {
    	/*
    	$application = $e->getApplication();
    	$servicemanager = $application->getServiceManager();
    	$eventManager = $application->getEventManager();
    	$sharedEventManager = $eventManager->getSharedManager();
    	 
    	$authService = $servicemanager->get('AuthService');
    	$eventManager->attach(MvcEvent::EVENT_ROUTE, array($authService, 'onRoute'), -100000 );
    	*/
    }

    public function getConfig()
    {
    	
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
    	return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
