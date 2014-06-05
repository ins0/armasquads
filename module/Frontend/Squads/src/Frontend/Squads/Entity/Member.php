<?php
namespace Frontend\Squads\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Squad
 *
 * @ORM\Entity
 * @ORM\Table(name="sqa_squad_member_4de785")
 */
class Member {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Squad", inversedBy="members")
     * @ORM\JoinColumn(name="SQA_ID", referencedColumnName="SQA_ID")
     */
    protected $squad;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="MEM_UID")
     */
    protected $uuid;

    /**
     * @ORM\Column(type="string", name="MEM_Username")
     */
    protected $username;

    /**
     * @ORM\Column(type="string", name="MEM_Name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="MEM_Email")
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="MEM_ICQ")
     */
    protected $icq;

    /**
     * @ORM\Column(type="string", name="MEM_Remark")
     */
    protected $remark;

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $icq
     */
    public function setIcq($icq)
    {
        $this->icq = $icq;
    }

    /**
     * @return mixed
     */
    public function getIcq()
    {
        return $this->icq;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return mixed
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param mixed $squad
     */
    public function setSquad($squad)
    {
        $this->squad = $squad;
    }

    /**
     * @return mixed
     */
    public function getSquad()
    {
        return $this->squad;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }


}