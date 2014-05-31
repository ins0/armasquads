<?php
namespace Frontend\Squads\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping as ORM;

/**
 * Squad
 *
 * @ORM\Entity
 * @ORM\Table(name="sqa_squads_6d4c2s")
 *
 */
class Squad {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="SQA_ID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Auth\Entity\Benutzer")
     * @ORM\JoinColumn(name="BEN_ID", referencedColumnName="BEN_ID")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", name="SQA_Tag")
     */
    protected $tag;

    /**
     * @ORM\Column(type="string", name="SQA_Name")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="SQA_Email")
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="SQA_Logo", nullable=true)
     */
    protected $logo;

    /**
     * @ORM\Column(type="string", name="SQA_Title")
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Member", mappedBy="squad", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function addMember( Member $member )
    {
        $member->setSquad($this);
        $this->members->add($member);
        return $this;
    }

    public function addMembers($members)
    {
        foreach( $members as $member )
        {
            $this->addMember($member);
        }
        return $this;
    }

    public function removeMember( $member )
    {
        $this->members->removeElement($member);
        return $this;
    }

    public function removeMembers( $members )
    {
        foreach( $members as $member )
        {
            $this->removeMember( $member );
        }
        return $this;
    }

    public function getSquadLogo()
    {
        $logoPath = '/uploads/logos/' . $this->getLogo() . '/' . $this->getLogo() . '.png';
        if( ! file_exists( ROOT_PATH . $logoPath ) )
            return false;

        return $logoPath;
    }

    public function getSquadLogoPaa()
    {
        $logoPath = '/uploads/logos/' . $this->getLogo() . '/' . $this->getLogo() . '.paa';
        if( ! file_exists( ROOT_PATH . $logoPath ) )
            return false;

        return $logoPath;
    }

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
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

    /**
     * @return mixed
     */
    public function getMembers()
    {
        return $this->members;
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
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
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



}