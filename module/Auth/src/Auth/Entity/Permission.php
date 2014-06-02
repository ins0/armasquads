<?php
namespace Auth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resources
 * 
 * @ORM\Entity
 * @ORM\Table(name="auth_zugriff_oj19de")
 * 
 * @property int $id
 * @property string $benutzerID
 * @property string $resourceID
 *
 */
class Permission {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="ZUG_ID")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="integer", name="GRU_ID")
	 */
	protected $gruppenID;
	
	/**
	 * @ORM\OneToOne(targetEntity="Role")
	 * @ORM\JoinColumn(name="GRU_ID", referencedColumnName="GRU_ID")
	 */
	private $gruppe;
	
	/**
	 * @ORM\Column(type="integer", name="RES_ID")
	 */
	protected $resourceID;
	

	/**
	 * @ORM\OneToOne(targetEntity="Resource")
	 * @ORM\JoinColumn(name="RES_ID", referencedColumnName="RES_ID")
	 */
	private $resource;
	
	
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