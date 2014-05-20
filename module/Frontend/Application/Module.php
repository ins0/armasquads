<?php
namespace Frontend\Application;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;

class Module
{
    public function onInit(EventInterface $e)
    {
    }
    
    public function onBootstrap(MvcEvent $e)
    {
    	$application =	$e->getApplication();
    	$serviceManager = $application->getServiceManager();
    	$eventManager = $application->getEventManager();
    	$sharedManager = $eventManager->getSharedManager();

    	// DISPATCH EVENT
    	$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {

    		$controller      = $e->getTarget();
    		$controllerClass = get_class($controller);
    		$moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));

    		if( $moduleNamespace == 'Frontend' ) {

    			// frontend layout
                // wenn der benutzer eingeloggt ist darf er das admin panel betreten?
                $authService = $e->getApplication()->getServiceManager()->get('AuthService');

                if( $authService->isLoggedIn() ) {
                    // layout für registrierte user
        			$controller->layout('layout/frontend/registered');
                } else {
                    // normal layout unregistriert
                    $controller->layout('layout/frontend');
                }
    		}

    	}, 50 );
    
    }

    public function getConfig()
    {
    	
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
