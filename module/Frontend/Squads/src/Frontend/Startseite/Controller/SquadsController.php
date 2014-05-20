<?php
namespace Frontend\Startseite\Controller;

use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class SquadsController extends AbstractFrontendController
{

    public function indexAction(){

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/index.phtml');

        return $viewModel;
    }
}
