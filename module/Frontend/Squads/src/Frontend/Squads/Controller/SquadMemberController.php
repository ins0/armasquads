<?php
namespace Frontend\Squads\Controller;

use Frontend\Squads\Form\Squad as SquadForm;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Zend\Mvc\Router\Console\Catchall;
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

        $squadEntityOriginal = clone $squadEntity;

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
        $viewModel->setTemplate('/squads/member/edit.phtml');
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('squad', $squadEntity);
        return $viewModel;
    }

}
