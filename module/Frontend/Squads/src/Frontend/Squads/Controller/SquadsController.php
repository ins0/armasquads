<?php
namespace Frontend\Squads\Controller;

use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class SquadsController extends AbstractFrontendController
{

    public function indexAction(){

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/index.phtml');

        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $userSquads = $squadRepo->findBy(array('user' => $this->identity() ));

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/index.phtml');
        $viewModel->setVariable('squads', $userSquads);
        return $viewModel;
    }
}
