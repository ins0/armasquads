<?php
namespace Frontend\Startseite\Controller;

use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\View\Model\ViewModel;

class StartseiteController extends AbstractFrontendController
{

    public function indexAction(){

        $userRepo = $this->getEntityManager()->getRepository('Auth\Entity\Benutzer');
        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $memberRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Member');

        // last user
        $lastUser = $userRepo->findOneBy(array(),array('id' => 'desc'));
        // total user
        $totalUsers = $userRepo->createQueryBuilder('c')->select('count(c.id)')->getQuery()->getSingleScalarResult();
        // total squads
        $totalSquads = $squadRepo->createQueryBuilder('c')->select('count(c.id)')->getQuery()->getSingleScalarResult();
        // total squads
        $totalSquadsMembers = $memberRepo->createQueryBuilder('c')->select('count(c.squad)')->getQuery()->getSingleScalarResult();
        // total images
        $totalSquadLogos = $squadRepo->createQueryBuilder('c')->select('count(c.logo)')->where('c.logo IS NOT NULL')->andWhere("c.logo != ''")->getQuery()->getSingleScalarResult();

        $viewModel = new ViewModel();
        $viewModel->setTemplate('frontend/startseite');
        $viewModel->setVariable('lastUser', $lastUser);
        $viewModel->setVariable('totalUsers', $totalUsers);
        $viewModel->setVariable('totalSquads', $totalSquads);
        $viewModel->setVariable('totalSquadsMembers', $totalSquadsMembers);
        $viewModel->setVariable('totalSquadLogos', $totalSquadLogos);

        return $viewModel;
    }
}
