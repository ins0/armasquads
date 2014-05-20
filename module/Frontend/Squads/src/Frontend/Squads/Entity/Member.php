<?php
namespace Frontend\Squads\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Squad
 *
 * @ORM\Entity
 * @ORM\Table(name="sqa_squad_member_4de785")
 * @ORM\Entity(repositoryClass="Auth\Repository\Role")
 * @property int $id
 * @property string $name
 * @property int $parent
 * @property int $supervisor
 *
 */
class Member {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="MEM_ID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="MEM_UID")
     */
    protected $tag;

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
     * @ORM\Column(type="integer", name="MEM_ICQ")
     */
    protected $logo;

    /**
     * @ORM\Column(type="string", name="MEM_Remark")
     */
    protected $remark;

    /**
     * @ORM\ManyToOne(targetEntity="Squad", inversedBy="members")
     * @ORM\JoinColumn(name="SQA_ID", referencedColumnName="SQA_ID")
     */
    protected $squad;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

}