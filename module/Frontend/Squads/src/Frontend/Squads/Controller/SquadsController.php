<?php
namespace Frontend\Squads\Controller;

use Frontend\Squads\Form\Squad as SquadForm;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\Mvc\Router\Console\Catchall;
use Zend\View\Model\ViewModel;

class SquadsController extends AbstractFrontendController
{

    public function indexAction(){

        $this->setAccess('frontend/squads/access');

        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $userSquads = $squadRepo->findBy(array('user' => $this->identity() ));

        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/index.phtml');
        $viewModel->setVariable('squads', $userSquads);
        return $viewModel;
    }

    public function deleteAction()
    {
        $this->setAccess('frontend/squads/delete');

        $squadID = (int) $this->params('id', null);

        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squadEntity = $squadRepo->findOneBy(array(
            'user' => $this->identity(),
            'id' => $squadID
        ));

        if( ! $squadEntity )
        {
            $this->flashMessenger()->addErrorMessage('Squad not found');
        }
        else
        {
            $this->flashMessenger()->addSuccessMessage('Squad successfully deleted!');
            $this->getEntityManager()->remove($squadEntity);
            $this->getEntityManager()->flush();
        }

        return $this->redirect()->toRoute('frontend/user/squads');
    }

    public function editAction()
    {
        $this->setAccess('frontend/squads/create');

        $squadID = (int) $this->params('id', null);

        $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squadEntity = $squadRepo->findOneBy(array(
            'user' => $this->identity(),
            'id' => $squadID
        ));
        $squadEntityOriginal = clone $squadEntity;

        if( ! $squadEntity )
        {
            $this->flashMessenger()->addErrorMessage('Squad not found');
            return $this->redirect()->toRoute('frontend/user/squads');
        }

        $form  = new SquadForm();
        $form->setEntityManager(
            $this->getEntityManager()
        );
        $form->init();
        $form->bind($squadEntity);

        if( $this->getRequest()->isPost() )
        {
            $form->setData(
                array_merge_recursive($_POST, $_FILES)
            );

            if( $form->isValid() )
            {
                /** @var \Frontend\Squads\Entity\Squad $squad */
                $squad = $form->getData();
                $squad->setUser(
                    $this->getEntityManager()->getReference('Auth\Entity\Benutzer', $this->identity()->id )
                );

                // move logo
                $logoSpecs = $squad->getLogo();
                if( $logoSpecs && $logoSpecs['error'] != 4 )
                {
                    $logoName =  md5(microtime(true) . uniqid(microtime(true)));
                    $logoPath = ROOT_PATH . '/uploads/logos/' . $logoName . '/';

                    $image = new \Imagick( $logoSpecs['tmp_name'] );
                    $image->stripimage();
                    $image->writeImage($logoSpecs['tmp_name']);
                    $image->destroy();
                    $image->clear();
                    unset($image);

                    // logo convert
                    Try {
                        mkdir( $logoPath );
                        $squadLogosFormat = array(32, 64);
                        foreach($squadLogosFormat as $format)
                        {
                            $image = new \Imagick( $logoSpecs['tmp_name'] );
                            $image->stripimage();
                            $image->setimageformat('jpg');
                            $image->adaptiveresizeimage($format, $format, false);
                            $image->writeImage($logoPath . $format . '_' . $logoName . '.jpg');
                            $image->destroy();
                            $image->clear();
                            unset($image);

                            $image = new \Imagick( $logoSpecs['tmp_name'] );
                            $image->stripimage();
                            $image->setimageformat('tga');
                            $image->adaptiveresizeimage($format, $format, false);
                            $image->writeImage($logoPath . $format . '_' . $logoName . '.paa');
                            $image->destroy();
                            $image->clear();
                            unset($image);
                        }

                        $squad->setLogo($logoName);

                    } Catch( \Exception $e )
                    {
                        $squad->setLogo(null);
                    }
                } else {

                    // no logo change
                    $squad->setLogo(
                        $squadEntityOriginal->getLogo()
                    );
                }

                $this->getEntityManager()->merge( $squad );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage('Squad sucessfully edited!');
                return $this->redirect()->toRoute('frontend/user/squads');
            }
            else
            {
                $form->populateValues($this->getRequest()->getPost());
            }
        }


        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/edit.phtml');
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }

    public function createAction()
    {
        $this->setAccess('frontend/squads/create');

        $form  = new SquadForm();
        $form->setEntityManager(
            $this->getEntityManager()
        );
        $form->init();

        if( $this->getRequest()->isPost() )
        {
            $form->setData(
                array_merge_recursive($_POST, $_FILES)
            );

            if( $form->isValid() )
            {
                /** @var \Frontend\Squads\Entity\Squad $squad */
                $squad = $form->getData();
                $squad->setUser(
                    $this->getEntityManager()->getReference('Auth\Entity\Benutzer', $this->identity()->id )
                );

                // move logo
                $logoSpecs = $squad->getLogo();
                $logoName =  md5(microtime(true) . uniqid(microtime(true)));
                $logoPath = ROOT_PATH . '/uploads/logos/' . $logoName . '/';

                $image = new \Imagick( $logoSpecs['tmp_name'] );
                $image->stripimage();
                $image->writeImage($logoSpecs['tmp_name']);
                $image->destroy();
                $image->clear();
                unset($image);

                // logo convert
                Try {
                    mkdir( $logoPath );
                    $squadLogosFormat = array(32, 64);
                    foreach($squadLogosFormat as $format)
                    {
                        $image = new \Imagick( $logoSpecs['tmp_name'] );
                        $image->stripimage();
                        $image->setimageformat('jpg');
                        $image->adaptiveresizeimage($format, $format, false);
                        $image->writeImage($logoPath . $format . '_' . $logoName . '.jpg');
                        $image->destroy();
                        $image->clear();
                        unset($image);

                        $image = new \Imagick( $logoSpecs['tmp_name'] );
                        $image->stripimage();
                        $image->setimageformat('tga');
                        $image->adaptiveresizeimage($format, $format, false);
                        $image->writeImage($logoPath . $format . '_' . $logoName . '.paa');
                        $image->destroy();
                        $image->clear();
                        unset($image);
                    }

                    $squad->setLogo($logoName);

                } Catch( \Exception $e )
                {
                    $squad->setLogo(null);
                }

                $this->getEntityManager()->persist( $squad );
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage('Squad sucessfully created!');
                return $this->redirect()->toRoute('frontend/user/squads');
            }
            else
            {
                $form->populateValues($this->getRequest()->getPost());
            }
        }


        $viewModel = new ViewModel();
        $viewModel->setTemplate('/squads/create.phtml');
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }
}
