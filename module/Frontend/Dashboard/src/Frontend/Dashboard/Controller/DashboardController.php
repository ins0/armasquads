<?php
namespace Frontend\Dashboard\Controller;

use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractFrontendController
{

    public function indexAction(){

        $this->setAccess('frontend/dashboard/access');

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/dashboard/index.phtml');

        return $viewModel;
    }
}
