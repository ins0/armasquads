<?php
namespace Frontend\Login\Controller;

use Auth\Entity\Benutzer;
use Frontend\Application\Controller\AbstractFrontendController;
use Frontend\Login\Form\Login;
use Auth\Acl\Acl;
use Frontend\Login\Form\Register;
use Zend\View\Model\ViewModel;
use Racecore\GATracking\GATracking;
use Racecore\GATracking\Tracking\Event;

class LoginController extends AbstractFrontendController
{

    public function registerAction()
    {
        $registerForm = new Register();
        $registerForm->setInputFilter(new \Frontend\Login\Form\Filter\Register(
            $this->getEntityManager()
        ));

        $loginForm = new Login();
        $loginForm->init();

        if( $this->request->isPost() ) {

            $registerForm->setData(
                $this->getRequest()->getPost()
            );

            if( $registerForm->isValid() )
            {
                $data = $registerForm->getData();

                $benutzer = new Benutzer();
                $benutzer->setUsername( $data['username'] );
                $benutzer->setPassword( $data['password'] );
                $benutzer->setEmail( $data['email'] );
                $benutzer->setDisabled( false );
                $benutzer->setRegisterDate( date('c') );

                $gruppe = $this->getEntityManager()->getReference('Auth\Entity\Role', 1);
                $benutzer->setGruppe($gruppe);

                $this->getEntityManager()->persist( $benutzer );
                $this->getEntityManager()->flush();

                /** @var GATracking $analytics */
                $analytics = $this->getServiceLocator()->get(GATracking::class);

                /** @var Event $eventTracker */
                $eventTracker = $analytics->createTracking('Event');
                $eventTracker->setEventCategory('User');
                $eventTracker->setEventAction('Register');
                $eventTracker->setEventLabel($benutzer->getUsername());
                $eventTracker->setEventValue($benutzer->getId());
                $analytics->sendTracking($eventTracker);

                // login
                /** @var Acl $authService */
                $authService = $this->getServiceLocator()->get('AuthService');
                $authService->instantLogin( $benutzer );

                return $this->redirect()->toRoute('frontend/user/home');

            } else {
                $registerForm->populateValues(
                    $this->getRequest()->getPost()
                );
            }
        }

        $viewModel = new ViewModel;
        $viewModel->setVariable('loginForm', $loginForm);
        $viewModel->setVariable('registerForm', $registerForm);
        $viewModel->setTemplate('/login/login.phtml');
        return $viewModel;
    }


    /**
     * Administrationsbereich Login
     *
     * @return ViewModel
     */
    public function loginAction() {

    	// wenn access vorhanden direkt weiter
    	if( $this->hasAccess('frontend/dashboard/access') ) {
    		return $this->redirect()->toRoute('frontend/user/home');
    	}

    	// form
        $registerForm = new Register();
    	$loginForm = new Login();
    	$loginForm->init();

        // fallback uri
        $fallbackUrl = $this->Params()->fromQuery('fallback_url', false);
        if( $fallbackUrl )
        {
            // check if fallback have no domain
            $urlParse = parse_url( $fallbackUrl );
            if( !$urlParse || !isset($urlParse['path'] ) )
            {
                // no valid fallback
                $fallbackUrl = false;
            } else {

                $fallbackUrl = $urlParse['path'];
            }
        }

        if( $this->request->isPost() ) {
    	
    		$username = $this->request->getPost('email');
    		$password = $this->request->getPost('password');
    		
    		$authService = $this->getServiceLocator()->get('AuthService');
            $loggedIn = $authService->login( $username, $password);

    		if( $loggedIn == Acl::LOGIN_WRONG ) {

    			$this->Message()->addErrorMessage('FRONTEND_LOGIN_AUTH_WRONG');

    		} elseif( $loggedIn == Acl::LOGIN_DISABLED ) {

    			$this->Message()->addInfoMessage('FRONTEND_LOGIN_AUTH_BANNED');
    		
    		} elseif ( $loggedIn == Acl::LOGIN_SUCCESS ) {

    			// last login
                /** @var Benutzer $benutzer */
    			$benutzer = $this->identity();
    			$benutzer->setLastLogin(date('c'));

    			$this->getEntityManager()->merge( $benutzer );
    			$this->getEntityManager()->flush();

                if( $fallbackUrl )
                {
                    // redirect to fallback url
                    return $this->redirect()->toUrl( $fallbackUrl );
                } else {
                    // redirect to user home
                    return $this->redirect()->toRoute('frontend/user/home');
                }
    		}
    	}
    	 
    	$viewModel = new ViewModel;
        $viewModel->setVariable('loginForm', $loginForm);
        $viewModel->setVariable('loginFallbackUrl', $fallbackUrl);
        $viewModel->setVariable('registerForm', $registerForm);
        $viewModel->setTemplate('/login/login.phtml');
    	return $viewModel;
    }

    /**
     * Administrationsbereich Logout
     *
     * @return mixed
     */
    public function logoutAction(){

        $authService = $this->getServiceLocator()->get('AuthService');

        if( $authService->hasIdentity() ) {

            $authService->clearIdentity();
            $this->Message()->addSuccessMessage('FRONTEND_LOGIN_AUTH_LOGOUT');
        }

    	return $this->redirect()->toRoute('frontend');
    }
}
