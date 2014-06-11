<?php
namespace Auth\Acl;

use Auth\Entity\Benutzer;

use Auth\Entity\Role;
use Zend\Authentication\AuthenticationService;

use Zend\Authentication\Storage\Session;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Permissions\Acl\Acl as ZendAcl;

use Zend\Permissions\Acl\Resource\GenericResource;

use Zend\Permissions\Acl\Role\GenericRole;

class Acl extends AuthenticationService {

	private $acl = null;
	private $sm = null;
	private $em = null;

    CONST LOGIN_SUCCESS = 1;
    CONST LOGIN_WRONG = 2;
    CONST LOGIN_DISABLED = 3;


    /**
     * Liefert die ACL
     *
     * @return null
     */
    public function getAcl(){

		return $this->acl;
	}

    /**
     * Liefert den FlashMessenger
     *
     * @return mixed
     */
    public function getMessenger(){

        return $this->sm->get('ControllerPluginManager')->get('Message');
    }

    /**
     * Registriert die Module aus der DB mit Zend/Auth
     * Setzt die Rechte der Gruppen
     *
     * @param $sm
     */
    public function __construct( $sm ) {



        $authSessionStorage = new Session('AUTH_IDENTITY');
        parent::__construct($authSessionStorage);

		$em = $sm->get('Doctrine\ORM\EntityManager');
		$acl = new ZendAcl();
		
		// add roles
		foreach( $em->getRepository('Auth\Entity\Role')->findBy(array(), array('parentId' => 'ASC')) as $role){
				
			if( $role->parent ) {
				$parentName = $role->parent->name;
			} else {
				$parentName = null;
			}
				
			$acl->addRole( new GenericRole( $role->name ), $parentName );
		}
		
		// add resources + action
		foreach( $em->getRepository('Auth\Entity\Resource')->findBy(array(), array('modul' => 'DESC')) as $resource){

            $ressouceName = $resource->modul;

            if( $resource->action ){
                $ressouceName .= '/' . $resource->action;
            }

            if( $resource->subAction ) {
                $ressouceName .= '/' . $resource->subAction;
            }

			$acl->addResource( new GenericResource(
                $ressouceName
			));
		}

        unset($ressouceName);
		
		// deny all
		$acl->deny( null );
		
		// add permissions
		foreach( $em->getRepository('Auth\Entity\Permission')->findAll() as $permission ){
			// allow
            $permissionName = $permission->resource->modul;

            if( $permission->resource->action ){
                $permissionName .= '/' . $permission->resource->action;
            }

            if( $permission->resource->subAction ) {
                $permissionName .= '/' . $permission->resource->subAction;
            }

			$acl->allow(
					$permission->gruppe->name,
                    $permissionName
			);
		}

		// register identity
		if( ! $this->hasIdentity() ) {

			// register as gast
			$benutzer = new Benutzer();
			$benutzer->setUsername('Unbekannter User');
			$benutzer->setId(0);
            $benutzer->setLoggedIn(false);

            $gruppe = new Role();
            $gruppe->id = 2;
			$gruppe->name = 'Gast';
            $gruppe->supervisor = 0;
			$benutzer->setGruppe($gruppe);
			
			
			if( !$benutzer ) {
				throw new \Exception('Gastbenutzer mit der ID -1 nicht vorhanden - bitte direkt in der Datenbank anlegen');	
			}

			$this->getStorage()->write( $benutzer );
		}
		
		// register acl in navigation
		\Zend\View\Helper\Navigation\AbstractHelper::setDefaultAcl( $acl );
		\Zend\View\Helper\Navigation\AbstractHelper::setDefaultRole( $this->getIdentity()->getGruppe()->name );

		$this->acl = $acl;
		$this->sm = $sm;
		$this->em = $em;
		
		return $this;
	}

    /**
     * Validiert einen Benutzer mit der DB
     *
     * @param $username
     * @param $password
     * @return int
     */
    public function login($email, $password) {

        /** @var Benutzer $benutzer */
		$benutzer = $this->em->getRepository('Auth\Entity\Benutzer')->findOneByEmail( $email );

        // old md5 back comp.
        if($benutzer && strlen($benutzer->getPassword()) == 32 && $benutzer->getPassword() == md5($password) )
        {
            $benutzer->setPassword( $password );
            $this->em->flush();
        }

        if( $benutzer && $benutzer->checkAgainstPassword( $password ) ) {

			if( $benutzer->getDisabled() == true ) {
				// user is blocked
                return self::LOGIN_DISABLED;
			}

            // success
			$benutzer->setLoggedIn(true);
			$this->getStorage()->write( $benutzer );

			return self::LOGIN_SUCCESS;			
		}
		
		return self::LOGIN_WRONG;
	}

    /**
     * Instant Login Auth
     *
     * @param Benutzer $benutzer
     * @return bool
     */
    public function instantLogin( Benutzer $benutzer )
    {
        $benutzer->setLoggedIn( true );
        $this->getStorage()->write( $benutzer );

        return true;
    }

	/**
	 * Mithilfe dieser Methode kann ein redirect durchgeführt werden.
     * Führt einen hart Redirect aus!
	 *
	 * @param String $route
	 *
	 */
	public function redirect( $route, $params = array(), $fallback_uri = false, $options = array() ) {
	
		$event = $this->sm->get('Application')->getMvcEvent();
        /** @var TreeRouteStack $router */
        $router = $event->getRouter();

        // route name
        $options['name'] = $route;

        // route fallback query
        if( $fallback_uri )
        {
            $options['query'] = array('fallback_url' => $_SERVER['REQUEST_URI']);
        }

		$url = $router->assemble(
            $params,
            $options
		);

        header('Location: ' . $url );
        die();
	}
	
	/**
	 * Ist der aktuelle Benutzer eingeloggt?
	 * @return boolean
	 */
	public function isLoggedIn() {
		
		return (bool) $this->getIdentity()->getLoggedIn();
		
		/**
		if( $this->getIdentity()->gruppe->name != 'Gast' ) {
			return true;
		}
		
		return false;
		**/
	}

    public function hasIdentity() {

        $parentHasIdentity = parent::hasIdentity();

        // gast account?
        if( $parentHasIdentity && $this->getIdentity()->getId() === 0 ) {

            // gast account - wie keine identity
            return false;
        }

        return $parentHasIdentity;
    }
	
	/**
	 * Liefert die Identität des Benutzers
	 * @return Benutzer
	 */
	public function getIdentity(){
		return $this->getStorage()->read();
	}
	
	/**
	 * has access?
	 * @param string $action
	 */
	public function hasAccess( $action ) {

        if( $this->getIdentity()->getGruppe()->supervisor === 1 )
        {
            // global admin
            return true;
        }

		Try {
			return $this->acl->isAllowed(
				$this->getIdentity()->getGruppe()->name,
				$action
			);
		} Catch( \Exception $e ) {

			// access not found return false;
			return false;
		}
		
		return false;
	}
	
}