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
     * @ORM\Column(type="string", name="KEY_Value")
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
     * @ORM\Column(type="integer", name="KEY_Requests")
     */
    protected $requests;

    /**
     * @ORM\Column(type="integer", name="KEY_Limit")
     */
    protected $limit;

    /**
     * @ORM\Column(type="integer", name="KEY_Status")
     */
    protected $status;

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = (bool) $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return (bool) $this->status;
    }

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
     * @return \DateTime
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @param $request
     */
    public function setLastRequest($request)
    {
        $this->lastRequest = $request;
    }

    /**
     * @param $value
     */
    public function setRequests($value)
    {
        $this->requests = $value;
    }

    /**
     * @return mixed
     */
    public function getRequests()
    {
        return $this->requests;
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

    /**
     * Update Request per Day
     */
    public function increaseRequest()
    {
        $this->requests = (((int) $this->getRequests()) + 1);
    }

    public function getDefaultRateLimit()
    {
        return 100;
    }

    /**
     * Check if API limit is exceeded
     *
     * @return bool
     */
    public function isLimitExceeded()
    {
        // check if first api call was made
        if( !$this->getLastRequest() || !$this->getRequests() )
            return false;

        // check requests vs limit
        return $this->getRequests() > $this->getLimit();
    }

    public function getRemainingRate()
    {
        $request = $this->getLimit() - $this->getRequests();
        return $request <= 0 ? 0 : $request;
    }

    public function getNextRateReset()
    {
        $date = new \DateTime('now');
        $minutes = $date->format('i');
        $seconds = $date->format('s');
        if($minutes > 0){
            $date->modify("+1 hour");
            $date->modify('-'.$minutes.' minutes');
            $date->modify('-'.$seconds.' seconds');
        }
        return $date;
    }

    public function checkForRateReset()
    {
        // check for rate reset
        if( $this->getLastRequest() && $this->getLastRequest()->getTimestamp() <= $this->getNextRateReset()->modify('-1 hour')->getTimestamp() )
        {
            $this->setRequests(1);
        }
    }

    public function update()
    {
        $this->setLastRequest(new \DateTime());
        $this->increaseRequest();
    }
}