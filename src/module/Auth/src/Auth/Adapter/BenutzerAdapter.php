<?php

namespace Auth\Adapter;

use Auth\Entity\Benutzer;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class BenutzerAdapter implements AdapterInterface
{
    /* @var EntityManager */
    private $entityManager;

    /* @var string */
    private $username;

    /* @var string */
    private $password;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Setzt die Benutzerinfos
     *
     * @author  Marco Rieger
     * @param $username
     * @param $password
     */
    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Auth gegen Benutzer Credentials
     *
     * @author  Marco Rieger
     * @return Result
     */
    public function authenticate()
    {
        $benutzerRepository = $this->entityManager->getRepository(Benutzer::class);

        /** @var Benutzer $benutzer */
        $benutzer = $benutzerRepository->findOneByEmail($this->username);

        // user not found or invalid inputs
        if (!$benutzer || !$this->username || !$this->password) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['FRONTEND_LOGIN_AUTH_WRONG']);
        }
        if ($benutzer && $benutzer->checkAgainstPassword($this->password)) {
            // check if user account is blocked
            if ($benutzer->isAccountDisabled() === true) {
                // denied - blocked account
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['FRONTEND_LOGIN_AUTH_BANNED']);
            }
            return new Result(Result::SUCCESS, $benutzer->getId());
        }
        // ohh snappp!! something went horribly wrong
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['FRONTEND_LOGIN_AUTH_WRONG']);
    }
}
