<?php
namespace Auth\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Crypt\Key\Derivation\Pbkdf2;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Zend\Stdlib\ArraySerializableInterface;
use ZfcRbac\Identity\IdentityInterface;

/**
 * Benutzer
 * 
 * @ORM\Entity
 * @ORM\Table(name="ben_benutzer_91c48c")
 * @ORM\Entity(repositoryClass="Auth\Repository\Benutzer")
 */
class Benutzer implements ArraySerializableInterface, IdentityInterface {

    public function exchangeArray (Array $array)
    {
        die('asd2');
    }

    public function getArrayCopy()
    {
        return [
            'id'            => $this->getId(),
            'username'      => $this->getUsername(),
            'email'         => $this->getEmail(),
            'registerDate'  => $this->getRegisterDate()->getTimestamp()
        ];
    }

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="BEN_ID")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="BEN_Username")
	 */
	protected $username;

    /**
     * @ORM\Column(type="string", name="BEN_Password")
     */
    protected $password;

    /**
     * @ORM\Column(type="string", name="BEN_Email")
     */
    protected $email;

    /**
	 * @ORM\Column(type="boolean", name="BEN_Disabled")
	 */
	protected $disabled;

    /**
     * @ORM\Column(type="datetime", name="BEN_LastLogin")
     */
    protected $lastLogin;

    /**
     * @ORM\Column(type="datetime", name="BEN_Register")
     */
    protected $registerDate;

    /**
     * @ORM\ManyToMany(targetEntity="Auth\Entity\Role")
     * @ORM\JoinTable(name="auth_user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="BEN_ID")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection;
    }

    /**
     * Checks if the Account is Disabled
     *
     * @author  Marco Rieger
     * @return bool
     */
    public function isAccountDisabled()
    {
        return (bool) $this->disabled;
    }

    /**
     * Set a new Account Password
     *
     * @author  Marco Rieger
     * @param $password
     */
    public function setPassword($password)
    {
        $salt = Rand::getBytes(32, true);
        $salt = Pbkdf2::calc('sha256', $password, $salt, 100000, 32);
        $bcryp = new Bcrypt();
        $bcryp->setSalt($salt);
        $this->password = $bcryp->create($password);
    }

    /**
     * Check a Password against this User
     *
     * @param $password
     * @return bool
     */
    public function checkAgainstPassword($password)
    {
        // verify old md5 passwords
        if ($isOldValid = ($this->password == md5($password))) {
            $this->setPassword($password);
            return $isOldValid;
        }

        $bcrypt = new Bcrypt();
        return $bcrypt->verify($password, $this->password);
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
    /**
     * Adds a role
     *
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);
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
     * Liefert den Letzten Login als DateTime
     * @return \DateTime
     */
    public function getLastLogin(){
        return $this->lastLogin;
    }

    public function setLastLogin($lastLogin){
        $this->lastLogin = $lastLogin;
    }

    public function setRegisterDate($registerDate){
        $this->registerDate = $registerDate;
    }

    public function getGravatar($size = 80)
    {
        return 'https://secure.gravatar.com/avatar/' . md5( $this->getEmail() ) . '?r=g&d=mm&s=' . $size;
    }

    /**
     * Liefert das Reg Date
     * @return \DateTime
     */
    public function getRegisterDate(){
        return $this->registerDate;
    }

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param mixed $gruppe
     */
    public function setGruppe($gruppe)
    {
        $this->gruppe = $gruppe;
    }

    /**
     * @return mixed
     */
    public function getGruppe()
    {
        return $this->gruppe;
    }

    /**
     * @param mixed $gruppenID
     */
    public function setGruppenID($gruppenID)
    {
        $this->gruppenID = $gruppenID;
    }

    /**
     * @return mixed
     */
    public function getGruppenID()
    {
        return $this->gruppenID;
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
     * @param mixed $loggedIn
     */
    public function setLoggedIn($loggedIn)
    {
        $this->loggedIn = $loggedIn;
    }

    /**
     * @return mixed
     */
    public function getLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
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
}
