<?php
namespace Frontend\Api\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Squad
 *
 * @ORM\Entity
 * @ORM\Table(name="api_keys_s83dks")
 * @ORM\Entity(repositoryClass="Frontend\Api\Repository\Key")
 *
 */
class Key {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="KEY_ID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="KEY")
     */
    protected $key;

    /**
     * @ORM\OneToOne(targetEntity="Auth\Entity\Benutzer")
     * @ORM\JoinColumn(name="BEN_ID", referencedColumnName="BEN_ID")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", name="KEY_LastRequest")
     */
    protected $lastRequest;

    /**
     * @ORM\Column(type="integer", name="KEY_RequestsPerDay")
     */
    protected $requestsPerDay;

    /**
     * @ORM\Column(type="integer", name="KEY_LIMIT")
     */
    protected $limit;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Last Successfully API Request
     */
    public function updateLastRequest()
    {
        $this->lastRequest = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Update Request per Day
     */
    public function updateRequestsPerDay()
    {
        $this->requestsPerDay = $this->getRequestsPerDay() + 1;
    }

    public function resetRequestsPerDay()
    {
        $this->requestsPerDay = 0;
    }

    /**
     * @return mixed
     */
    public function getRequestsPerDay()
    {
        return $this->requestsPerDay;
    }

    /**
     * Check if API limit is exceeded
     *
     * @return bool
     */
    public function isLimitExceeded()
    {
        return (bool) !$this->getRequestsPerDay() >= $this->getLimit();
    }

    public function getRemainingLimit()
    {
        return $this->getLimit() - $this->getRequestsPerDay();
    }

    public function getLimitResetDate()
    {
        return strtotime('tomorrow');
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }
}