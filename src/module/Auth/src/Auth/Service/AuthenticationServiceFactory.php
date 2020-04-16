<?php

namespace Auth\Service;

use Auth\Adapter\AuthBenutzerAdapter;
use Auth\Adapter\BenutzerAdapter;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\Storage\Session;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class AuthenticationServiceFactory
 *
 * @author        Marco Rieger
 * @copyright     Copyright (c) 2013 AUBI-plus GmbH (http://www.aubi-plus.de)
 * @package       Auth\Service
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create Service
     *
     * @author  Marco Rieger
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager
     * @return AuthenticationService|mixed
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager)
    {
        /* @var EntityManager $entityManager */
        $entityManager = $serviceManager->get(EntityManager::class);

        /* @var EventManager $eventManager */
        $eventManager = $serviceManager->get('EventManager');

        $authBenutzerAdapter = new BenutzerAdapter($entityManager);
        $sessionStorage = new Session(AuthenticationService::SESSION_NAMESPACE, null, null);

        return new AuthenticationService(
            $eventManager,
            $entityManager,
            $sessionStorage,
            $authBenutzerAdapter
        );
    }
}
