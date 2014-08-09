<?php
namespace Frontend\Api\Controller;

use Frontend\Api\Entity\Key;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\Math\Rand;
use Zend\View\Model\ViewModel;

class ApiController extends AbstractFrontendController
{

    public function indexAction(){

        $this->requireLogin();

        $keyRepo = $this->getEntityManager()->getRepository('Frontend\Api\Entity\Key');
        $userKey = $keyRepo->findOneBy(array('user' => $this->identity() ));

        if( $this->getRequest()->getQuery('getKey') == 1 && !$userKey )
        {
            // add to api
            $keyChars = array_merge(
                range(0,9),
                range('A', 'Z'),
                range('a', 'z')
            );
            $apiKey = Rand::getString(50, implode('',$keyChars));

            $userKey = new Key();
            $userKey->setKey($apiKey);
            $userKey->setLimit($userKey->getDefaultRateLimit());
            $userKey->setUser(
                $this->getEntityManager()->getReference('Auth\Entity\Benutzer', $this->identity()->getId() )
            );
            $userKey->setRequests(1);
            $userKey->setStatus(1);

            Try {
            $this->getEntityManager()->persist($userKey);
            $this->getEntityManager()->flush();
            }catch(\Exception $e)
            {
                echo $e->getMessage();
                die();
            }
            $this->flashMessenger()->addSuccessMessage('Nice! Subscribe to API successful. Can\'t wait to hear about your Application');
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/api/index.phtml');
        $viewModel->setVariable('userKey', $userKey);
        return $viewModel;
    }
}