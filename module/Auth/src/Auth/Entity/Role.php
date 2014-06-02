<?php
namespace Auth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Benutzer
 * 
 * @ORM\Entity
 * @ORM\Table(name="auth_gruppen_a7d451")
 * @ORM\Entity(repositoryClass="Auth\Repository\Role")
 * @property int $id
 * @property string $name
 * @property int $parent
 * @property int $supervisor
 *
 */
class Role {
	
    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="children")
     * @ORM\JoinColumn(name="GRU_Parent", referencedColumnName="GRU_ID")
     */
    private $parent;
	
	/**
	 * @ORM\OneToMany(targetEntity="Role", mappedBy="parent")
	 */
	private $children;
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="GRU_ID")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="GRU_Name")
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="integer", name="GRU_Parent") 
	 */
	protected $parentId;
	
	/**
	 * @ORM\Column(type="integer", name="GRU_Supervisor")
	 */
	protected $supervisor;
	
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