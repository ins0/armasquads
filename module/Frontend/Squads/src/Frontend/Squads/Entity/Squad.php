<?php
namespace Frontend\Squads\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping as ORM;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Squad
 *
 * @ORM\Entity
 * @ORM\Table(name="sqa_squads_6d4c2s")
 * @ORM\Entity(repositoryClass="Frontend\Squads\Repository\Squad")
 *
 */
class Squad implements ArraySerializableInterface {

    public function exchangeArray (Array $array)
    {
        die('asd2');
    }

    public function getArrayCopy()
    {
        return [
            'id'            =>  $this->getId(),
            'privateID'     =>  $this->getPrivateID(),
            'tag'           =>  $this->getTag(),
            'name'          =>  $this->getName(),
            'email'         =>  $this->getEmail(),
            'logo'          =>  $this->getSquadLogoPaa(),
            'homepage'      =>  $this->getHomepage(),
            'title'         =>  $this->getTitle(),
        ];
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="SQA_ID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="SQA_PrivateID")
     */
    protected $privateID;

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
     * @ORM\Column(type="string", name="SQA_Homepage")
     */
    protected $homepage;

    /**
     * @ORM\Column(type="string", name="SQA_Title")
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Member", mappedBy="squad", cascade={"ALL"}, orphanRemoval=true)
     * @ORM\OrderBy({"name" = "ASC", "uuid" = "ASC"})
     */
    protected $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function addMember( Member $member )
    {
        foreach($this->members as $memberCheck )
        {
            if( $memberCheck->getUuid() == $member->getUuid() )
            {
                // member allready in the group do nohting with this
                return $this;
            }
        }

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
            return null;

        return $logoPath;
    }

    public function getSquadLogoPaa()
    {
        $logoPath = '/uploads/logos/' . $this->getLogo() . '/' . $this->getLogo() . '.paa';
        if( ! file_exists( ROOT_PATH . $logoPath ) )
            return null;

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

    /**
     * @param mixed $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * @return mixed
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * @param mixed $privateID
     */
    public function setPrivateID($privateID)
    {
        $this->privateID = $privateID;
    }

    /**
     * @return mixed
     */
    public function getPrivateID()
    {
        return $this->privateID;
    }

}