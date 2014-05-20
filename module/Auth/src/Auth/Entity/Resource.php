<?php
namespace Auth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Resources
 * 
 * @ORM\Entity
 * @ORM\Table(name="AUTH_Resourcen_D74FW1")
 * 
 * @property int $id
 * @property string $modul
 * @property string $action
 * @property string $subAction
 * @property string $description
 *
 */
class Resource {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="RES_ID")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string", name="RES_Modul")
	 */
	protected $modul;
	
	/**
	 * @ORM\Column(type="string", name="RES_Action")
	 */
	protected $action;

    /**
     * @ORM\Column(type="string", name="RES_SubAction")
     */
    protected $subAction;

    /**
     * @ORM\Column(type="string", name="RES_Description")
     */
    protected $description;

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