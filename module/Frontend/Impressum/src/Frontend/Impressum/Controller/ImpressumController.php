<?php
namespace Frontend\Impressum\Controller;

use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class ImpressumController extends AbstractFrontendController
{

    public function indexAction(){

        $viewModel = new ViewModel();
        $viewModel->setTemplate('frontend/impressum');

        return $viewModel;
    }
}
