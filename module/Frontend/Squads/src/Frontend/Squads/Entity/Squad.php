<?php
namespace Frontend\Squads\Entity;

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
     * @ORM\Column(type="string", name="SQA_Logo")
     */
    protected $logo;

    /**
     * @ORM\Column(type="string", name="SQA_Title")
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Member", mappedBy="squad")
     */
    protected $members;

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