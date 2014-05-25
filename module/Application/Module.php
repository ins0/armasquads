<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Doctrine\Common\Util\Debug;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{

    private function dispatchEvent(MvcEvent $e)
    {
        // TARGET & SERVICEMANAGER
        $target = $e->getTarget();
        $serviceManager = $e->getApplication()->getServiceManager();

        // CONFIG
        $config          = $e->getApplication()->getServiceManager()->get('config');

        // TRANSLATOR
        /** @var Translator $translator */
        $translator = $serviceManager->get('MvcTranslator');

        // LANG CONFIG
        $languages = $config['languages']['available'];
        $fallbackLocale = $config['languages']['fallback'];

        // ROUTER PARAM LOCALE
        $routeMatch = $e->getRouteMatch();
        if( $routeMatch )
        {
            $urlLocale = $routeMatch->getParam('locale');
        } else {
            /**
             * @todo
             * evtl hier noch aus der request url versuchen die locale zu lesen
             */
            $urlLocale = $fallbackLocale;
        }

        // ROUTER
        /** @var \Zend\Mvc\Router\Http\TreeRouteStack $router */
        $router = $e->getRouter();

        // check if language is present
        $usedLocale = ( isset( $languages[$urlLocale] ) ? $languages[$urlLocale] : false );
        $usedLocaleShort = ( isset( $languages[$urlLocale] ) ? $urlLocale : false );

        // language not found redirect to fallback lang
        if( ! $usedLocale && ! $usedLocaleShort ) {

            // wrong locale request
            $e->getRouteMatch()->setParam('locale', $fallbackLocale );

            $url = $e->getRouter()->assemble(
                $e->getRouteMatch()->getParams(),
                array('name' => $e->getRouteMatch()->getMatchedRouteName() )
            );

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            return $response;
        }

        // add current locale as default router param
        $router->setDefaultParam('locale', $usedLocaleShort);
        $translator->setLocale( $usedLocale );
        $translator->setFallbackLocale( $usedLocale );
        \Locale::setDefault( $usedLocale );
    }

    public function onBootstrap(MvcEvent $e)
    {
    	$application =	$e->getApplication();
    	$eventManager = $application->getEventManager();
    	$sharedManager = $eventManager->getSharedManager();

    	// Route Listener
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        // DISPATCH EVENT
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function(\Zend\Mvc\MvcEvent $e) {
            $this->dispatchEvent($e);
        }, 1000 );
        $eventManager->attach('dispatch.error', function(\Zend\Mvc\MvcEvent $e) {
            $this->dispatchEvent($e);
        }, 1000 );



        
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
