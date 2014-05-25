<?php
namespace Frontend\Squads\Controller;

use Doctrine\Common\Util\Debug;
use Frontend\Squads\Form\Member;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class SquadMemberController extends AbstractFrontendController
{

    public function editAction()
    {
        $this->setAccess('frontend/squads/create');

        $squadID = (int) $this->params('id', null);

        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squadEntity = $squadRepo->findOneBy(array(
            'user' => $this->identity(),
            'id' => $squadID
        ));

        if( ! $squadEntity )
        {
            $this->flashMessenger()->addErrorMessage('Squad not found');
            return $this->redirect()->toRoute('frontend/user/squads');
        }

        $form  = new Member();
        $form->setEntityManager(
            $this->getEntityManager()
        );
        $form->init($squadEntity);

        if( $this->getRequest()->isPost() )
        {
            $form->setData(
                $this->getRequest()->getPost()
            );

            if( $form->isValid() )
            {
                $members = $form->getData();

                $this->getEntityManager()->persist( $members );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage('Members sucessfully edited!');
                return $this->redirect()->refresh();
            }
            else
            {
                $form->populateValues($this->getRequest()->getPost());
            }
        }


        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/member/edit.phtml');
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('squad', $squadEntity);
        return $viewModel;
    }

}
