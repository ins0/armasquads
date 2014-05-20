<?php
namespace Auth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Benutzer
 * 
 * @ORM\Entity
 * @ORM\Table(name="BEN_Benutzer_91C48C")
 * @ORM\Entity(repositoryClass="Auth\Repository\Benutzer")
 * @property int $id
 * @property int $gruppenID
 * @property \Auth\Entity\Role $gruppe
 * @property string $username
 * @property string $password
 * @property bool $loggedIn
 * @property bool $disbaled
 * @property datetime $lastLogin
 * @property datetime $registerDate
 *
 */
class Benutzer {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="BEN_ID")
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
	protected $gruppe;

	/**
	 * @ORM\Column(type="string", name="BEN_Username")
	 */
	protected $username;
	
	/**
	 * @ORM\Column(type="string", name="BEN_Password")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="boolean", name="BEN_Disabled")
	 */
	protected $disabled;

    /**
     * @ORM\Column(type="string", name="BEN_LastLogin")
     */
    protected $lastLogin;

    /**
     * @ORM\Column(type="string", name="BEN_Register")
     */
    protected $registerDate;
	
	/**
	 * holds if the user is logged in
	 * @var bool
	 */
	protected $loggedIn = false;

    /**
     * Liefert den Letzten Login als DateTime
     * @return \DateTime
     */
    public function getLastLogin(){
        return new \DateTime( $this->lastLogin );
    }

    /**
     * Liefert das Reg Date
     * @return \DateTime
     */
    public function getRegisterDate(){
        return new \DateTime( $this->registerDate );
    }
	
	/**
	 * Magic getter to expose protected properties.
	 *
	 * @param string $property
	 * @return mixed
	 */
	public function __get($property)
	{
		if( method_exists( $this, 'get' . ucfirst( $property ) )) {
			return $this->{'get' . ucfirst( $property ) }();
		}
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