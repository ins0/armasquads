<?php
namespace Frontend\Squads\Controller;

use Frontend\Squads\Form\Squad as SquadForm;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class SquadsController extends AbstractFrontendController
{

    public function indexAction(){

        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $userSquads = $squadRepo->findBy(array('user' => $this->identity() ));

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/index.phtml');
        $viewModel->setVariable('squads', $userSquads);
        return $viewModel;
    }

    public function createAction()
    {
        $form  = new SquadForm();
        $form->setEntityManager(
            $this->getEntityManager()
        );
        $form->init();


        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/create.phtml');
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }
}
