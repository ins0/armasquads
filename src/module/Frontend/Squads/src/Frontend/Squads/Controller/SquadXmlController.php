<?php
namespace Frontend\Squads\Controller;

use Frontend\Squads\Entity\Squad;
use Frontend\Application\Controller\AbstractDoctrineController;
use Frontend\Application\Controller\AbstractFrontendController;
use Racecore\GATracking\GATracking;
use Racecore\GATracking\Tracking\Event;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

class SquadXmlController extends AbstractFrontendController
{
    public function squadFileAction()
    {
        $squadID = $this->params('id', null);
        $squadReposiory = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');

        /** @var Squad $squad */
        $squad = $squadReposiory->findOneBy( array('privateID' => $squadID ));
        if( ! $squad )
        {
            return $this->getResponse()->setStatusCode(404);
        }

        /** @var GATracking $analytics */
        $analytics = $this->getServiceLocator()->get(GATracking::class);
        $analytics->setClientId($_SERVER['REMOTE_ADDR']);

        /** @var Event $eventTracker */
        $eventTracker = $analytics->createTracking('Event');
        $eventTracker->setEventCategory('Squadfile');
        $eventTracker->setEventAction('Request');
        $eventTracker->setEventLabel($squad->getName() . ' ('.$squadID.')');
        $eventTracker->setEventValue($squad->getId());
        $analytics->sendTracking($eventTracker);

        // check logo
        $squadImageService = $this->getServiceLocator()->get('SquadImageService');
        $squadLogoFile = $squadImageService->getServerSquadLogo( $squad );

        if( $squadLogoFile )
        {
            if( $squad->getSquadLogoPaa() )
            {
                $squadLogoFile = basename($squad->getSquadLogoPaa());
            } else {
                $squadLogoFile = basename($squad->getSquadLogo());
            }
        } else {
            $squadLogoFile = false;
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('squad', $squad);
        $viewModel->setVariable('logoFile', $squadLogoFile);
        $viewModel->setTemplate('/squads/xml/squad.xml');

        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);

        $response = $this->getResponse();

        $headers = $this->response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/xml');
        $headers->addHeaderLine('Content-Length', strlen($html));

        $response->setContent($html);

        return $response;
    }

    public function logoFileAction()
    {
        $squadID = $this->params('id', null);
        $squadReposiory = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');

        /** @var Squad $squad */
        $squad = $squadReposiory->findOneBy( array('privateID' => $squadID ));

        if( ! $squad )
        {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            $response->setContent('');
            return $response;
        }

        $squadImageService = $this->getServiceLocator()->get('SquadImageService');
        $squadLogoPath = $squadImageService->getServerSquadLogo( $squad );

        if( ! $squadLogoPath )
        {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            $response->setContent('');
            return $response;
        }

        $response = $this->getResponse();
        if ($response instanceof Response) {
            $headers = $this->response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'image/plain');
        }

        if( $squad->getSquadLogoPaa() )
        {
            // return paa
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($squad->getSquadLogoPaa()));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize(ROOT_PATH . $squad->getSquadLogoPaa() ));
            ob_clean();
            flush();

            readfile( ROOT_PATH . $squad->getSquadLogoPaa() );
            die();

        }

        // return normal image
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($squad->getSquadLogo()));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize(ROOT_PATH . $squad->getSquadLogo() ));
        ob_clean();
        flush();

        $image = new \Imagick( ROOT_PATH . $squad->getSquadLogo() );
        $image->setImageBackgroundColor('white');
        $image->setimageformat('jpg');
        echo $image;
        die();
    }

    public function pngFileAction()
    {
        $squadID = (int) $this->params('id', null);
        $squadReposiory = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');

        /** @var Squad $squad */
        $squad = $squadReposiory->findOneBy( array('privateID' => $squadID ));
        $squadLogoPath = ROOT_PATH . $squad->getSquadLogo(64);

        if( ! $squad || !$squadLogoPath || !file_exists($squadLogoPath) )
        {
            return $this->getResponse()->setStatusCode(404);
        }

        $response = $this->getResponse();
        if ($response instanceof Response) {
            $headers = $this->response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'image/png; charset=utf-8');
        }

        return $this->getResponse()->setContent(
            readfile( $squadLogoPath )
        );
    }

    public function dtdFileAction()
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            $headers = $this->response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'application/xml-dtd');
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('/squads/xml/squad.dtd');
        return $viewModel;
    }

    public function xslFileAction()
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            $headers = $this->response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'application/xml; charset=utf-8');
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('/squads/xml/squad.xsl');
        return $viewModel;
    }

    public function cssFileAction()
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            $headers = $this->response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'application/xml; charset=utf-8');
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('/squads/xml/squad.css');
        return $viewModel;
    }
}
