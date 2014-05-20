<?php
namespace Frontend\Login\Controller;

use Frontend\Application\Controller\AbstractFrontendController;
use Frontend\Login\Form\Login;
use Auth\Acl\Acl;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractFrontendController
{

    public function registerAction()
    {
        $viewModel = new ViewModel;
        $viewModel->setVariable('loginForm', "");
        $viewModel->setTemplate('/login/register.phtml');
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
    	$loginForm = new Login();
    	$loginForm->init();

    	if( $this->request->isPost() ) {
    	
    		$username = $this->request->getPost('username');
    		$password = $this->request->getPost('password');
    		
    		$authService = $this->getServiceLocator()->get('AuthService');
            $loggedIn = $authService->login( $username, $password);

    		if( $loggedIn == Acl::LOGIN_WRONG ) {

    			$this->Message()->addErrorMessage('ADMIN_LOGIN_TEXT_LOGIN_WRONG');

    		} elseif( $loggedIn == Acl::LOGIN_DISABLED ) {

    			$this->Message()->addInfoMessage('ADMIN_LOGIN_TEXT_LOGIN_DISABLED');
    		
    		} elseif ( $loggedIn == Acl::LOGIN_SUCCESS ) {

    			// last login
    			$benutzer = $this->identity();
    			$benutzer->lastLogin = date('c');

    			$this->getEntityManager()->merge( $benutzer );
    			$this->getEntityManager()->flush();
    			
    			return $this->redirect()->toRoute('frontend/user/home');
    		}
    	}
    	 
    	$viewModel = new ViewModel;
    	$viewModel->setVariable('loginForm', $loginForm);
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
            $this->Message()->addSuccessMessage('LOGIN_TEXT_LOGOUT_SUCCESS');
        }

    	return $this->redirect()->toRoute('frontend');
    }
}
