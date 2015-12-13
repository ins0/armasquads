<?php

namespace Auth\Service;

use Auth\Entity\Benutzer;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService as ZendAuthService;
use Zend\Authentication\Result;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter;
use Zend\Cache\Storage as Cache;
use Zend\EventManager\EventManager;
use Zend\EventManager\ResponseCollection;
use Zend\Http\Response;
use Zend\View\Helper;

class AuthenticationService extends ZendAuthService
{
    const SESSION_NAMESPACE = AuthenticationService::class;
    const EVENT_AUTHENTICATE = 'auth.login';

    /* @var EntityManager */
    protected $entityManager;

    /* @var EventManager */
    protected $eventManager;

    /**
     * @param EventManager $eventManager
     * @param EntityManager|null $entityManager
     * @param Storage\StorageInterface|null $storageInterface
     * @param Adapter\AdapterInterface|null $adapterInterface
     */
    public function __construct(
        EventManager $eventManager,
        EntityManager $entityManager,
        Storage\StorageInterface $storageInterface = null,
        Adapter\AdapterInterface $adapterInterface = null
    ) {
        parent::__construct($storageInterface, $adapterInterface);

        $this->entityManager = $entityManager;
        $this->eventManager = $eventManager;
    }

    /**
     * Authenticate Benutzer
     *
     * @author  Marco Rieger
     * @param Adapter\AdapterInterface|null $adapter
     * @return Result
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        $loginResult = parent::authenticate($adapter);
        $benutzerEntity = $this->getIdentity();

        // trigger auth event
        /** @var ResponseCollection $result */
        $result = $this->eventManager->trigger($this::EVENT_AUTHENTICATE, $this, [
            'benutzer' => $benutzerEntity,
            'result' => $loginResult,
        ]);

        // save last login
        if ($loginResult->isValid()) {
            $benutzerEntity->setLastLogin(new \DateTime);
            $this->entityManager->flush();
        }

        // check if events stopped the process and forcing a redirect
        if ($result->stopped() && $result->offsetGet(0) instanceof Result) {
            return $result->offsetGet(0);
        }

        return $loginResult;
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return Benutzer|null
     */
    public function getIdentity()
    {
        $identityData = parent::getIdentity();

        if ($identityData) {
            return $this->entityManager->getReference(Benutzer::class, $identityData);
        }

        return $identityData;
    }

    /**
     * Force the current Login Session change to a new Benutzer Session
     * WARNING!!! This Function ignores User Passwords!
     *
     * @param Benutzer $benutzer
     * @return Result
     */
    public function forceLogin(Benutzer $benutzer)
    {
        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        $this->getStorage()->write($benutzer->getId());

        return new Result(Result::SUCCESS, $benutzer);
    }
}
