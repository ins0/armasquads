<?php
namespace Frontend\Api\v1\Controller;

use Doctrine\Common\Util\Debug;
use Frontend\Api\Controller\AbstractJsonController;
use Frontend\Api\Controller\JsonErrorResponse;
use Frontend\Api\Response\ApiResponse;
use Frontend\Squads\Form\Squad;
use Zend\Stdlib\ArraySerializableInterface;

class MembersController extends AbstractJsonController
{
    /**
     * Fetch all user squads
     * @return array
     */
    public function fetchAll(){

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $userSquads = $squadRepository->findBy(array('user' => $this->getApiIdentity()), array('id' => 'desc'));

        $result = [];
        foreach($userSquads as $squad)
        {
            if( $squad instanceof ArraySerializableInterface )
                $result[] = $squad->getArrayCopy();
        }

        return new ApiResponse($result, null, 200);
    }

    /**
     * Fetch user squad
     * @return array
     */
    public function fetch(){

        $squadId = (int) $this->params('id', 0);

        if( $squadId <= 0 )
        {
            return new ApiResponse(null, 'missing squad parameter id', 404);
        }

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => $squadId), array('id' => 'desc'));

        if( !$squad )
        {
            return new ApiResponse(null, 'squad not found', 404);
        }

        if( $squad instanceof ArraySerializableInterface )
        {
                $result = $squad->getArrayCopy();
        }

        return new ApiResponse($result, null, 200);
    }

    /**
     * Update a user Squad
     *
     * @return ApiResponse
     */
    public function update()
    {
        $postData = $this->getArrayPostData();

        if( !isset($postData['id']) || empty($postData['id']) )
            return new ApiResponse(null, 'squad [id] missing', 400);

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => (int)$postData['id']));

        if( ! $squad )
            return new ApiResponse(null, 'squad for update not found', 404);

        $originalEntity = $squad;

        $squadForm = new Squad();
        $squadForm->setServiceManager($this->getServiceLocator());
        $squadForm->setUseInputFilterDefaults(false);
        $squadForm->init($squad);

        // logo
        if(isset($postData['logo']))
        {
            $base64logo = base64_decode($postData['logo']);
            $logo['logo'] = Array();
            $logo['logo']['error'] = 0;

            $logoTmpFile = tempnam(get_cfg_var('upload_tmp_dir'), null);
            file_put_contents($logoTmpFile, $base64logo);
            $logo['logo']['tmp_name'] = realpath($logoTmpFile);
            $logo['logo']['name'] = basename($logoTmpFile . '.png');
            $logo['logo']['type'] = 'image/png';
            $logo['logo']['size'] = strlen($base64logo);
            $_FILES = $logo;
        }

        $squadForm->setData(
            array_merge_recursive($postData, $_FILES)
        );
        if( $squadForm->isValid() )
        {
            $squad = $squadForm->getData();
            $squad->setUser($this->getApiIdentity());

            $uploadedLogoSpecs = $squad->getLogo();
            // logo set?
            if( $uploadedLogoSpecs )
            {
                $squadImageService = $this->getServiceLocator()->get('SquadImageService');
                $squadLogoID = $squadImageService->saveLogo(
                    $uploadedLogoSpecs
                );
                if( $squadLogoID !== false )
                {
                    $squad->setLogo($squadLogoID);
                } else {
                    $squad->setLogo(
                        $originalEntity->getLogo() ? $originalEntity->getLogo() : null
                    );
                }
            } else {
                // no logo change
                $squad->setLogo(
                    $originalEntity->getLogo() ? $originalEntity->getLogo() : null
                );
            }

            $this->getEntityManager()->merge($squad);
            $this->getEntityManager()->flush($squad);

            // save
            return new ApiResponse($squad->getArrayCopy(), null, 201);

        } else {
            return new ApiResponse(null, $squadForm->getMessages(), 422);
        }
    }

    /**
     * Delete user squad
     * @return ApiResponse
     */
    public function delete()
    {
        $squadId = $this->params('id', false);
        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $userSquad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => (int) $squadId));

        if( !$userSquad )
        {
            $errorResponse = new ApiResponse();
            if( $squadId === false )
            {
                $errorResponse->setStatusCode(400);
                $errorResponse->setErrorMessage('missing parameter id');
            } else {
                $errorResponse->setStatusCode(404);
                $errorResponse->setErrorMessage('squad not found');
            }

            return $errorResponse;
        }

        $this->getEntityManager()->remove($userSquad);
        $this->getEntityManager()->flush();

        return new ApiResponse(null, null, 200);
    }

    public function create()
    {
        $squadForm = new Squad();
        $squadForm->setServiceManager($this->getServiceLocator());
        $squadForm->setUseInputFilterDefaults(false);
        $squadForm->init();

        $postData = $this->getArrayPostData();

        // logo
        if(isset($postData['logo']))
        {
            $base64logo = base64_decode($postData['logo']);
            $logo['logo'] = Array();
            $logo['logo']['error'] = 0;

            $logoTmpFile = tempnam(get_cfg_var('upload_tmp_dir'), null);
            file_put_contents($logoTmpFile, $base64logo);
            $logo['logo']['tmp_name'] = realpath($logoTmpFile);
            $logo['logo']['name'] = basename($logoTmpFile . '.png');
            $logo['logo']['type'] = 'image/png';
            $logo['logo']['size'] = strlen($base64logo);
            $_FILES = $logo;
        }

        $squadForm->setData(
            array_merge_recursive($postData, $_FILES)
        );
        if( $squadForm->isValid() )
        {
            $squad = $squadForm->getData();
            $squad->setUser($this->getApiIdentity());

            $uploadedLogoSpecs = $squad->getLogo();
            // logo set?
            if( $uploadedLogoSpecs )
            {
                $squadImageService = $this->getServiceLocator()->get('SquadImageService');
                $squadLogoID = $squadImageService->saveLogo(
                    $uploadedLogoSpecs
                );
                if( $squadLogoID !== false )
                {
                    $squad->setLogo($squadLogoID);
                } else {
                    $squad->setLogo(null);
                }
            } else {
                // no logo change
                $squad->setLogo(null);
            }

            // new squad url
            $squadRepo = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
            $squadRepo->createUniqueToken($squad);

            $this->getEntityManager()->persist($squad);
            $this->getEntityManager()->flush($squad);

            // save
            return new ApiResponse($squad->getArrayCopy(), null, 201);

        } else {
            return new ApiResponse(null, $squadForm->getMessages(), 422);
        }
    }
}
