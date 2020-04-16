<?php
namespace Frontend\Dashboard\Controller;

use Auth\Form\Account;
use Auth\Form\PasswordReset;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractFrontendController
{

    public function indexAction(){

        $form = new Account($this->getServiceLocator());
        if( $this->getRequest()->isPost() )
        {
            $form->setData($this->getRequest()->getPost());

            $resetPassword = $this->getRequest()->getPost('resetPassword');
            if( isset($resetPassword['resetPasswordSubmit'] ) )
            {
                $form->setValidationGroup(array(
                    'csrf',
                    'resetPassword' => array(
                        'oldPass',
                        'newPass',
                        'newPassConfirm'
                    )
                ));

                if($form->isValid())
                {
                    $formData = $form->getData();
                    $currentBenutzer = $this->getEntityManager()->getRepository('Auth\Entity\Benutzer')->find($this->identity());
                    $currentBenutzer->setPassword($formData['resetPassword']['newPass']);
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->addSuccessMessage('Password changed');
                }
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/account/index.phtml');
        $viewModel->setVariable('form', $form);

        return $viewModel;
    }

}
