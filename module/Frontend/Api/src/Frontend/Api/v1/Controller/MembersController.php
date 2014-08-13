<?php
namespace Frontend\Api\v1\Controller;

use Doctrine\Common\Util\Debug;
use Frontend\Api\Controller\AbstractJsonController;
use Frontend\Api\Controller\JsonErrorResponse;
use Frontend\Api\Response\ApiResponse;
use Frontend\Squads\Form\Member;
use Frontend\Squads\Form\MemberFieldset;
use Frontend\Squads\Form\Squad;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\ArraySerializableInterface;

class MembersController extends AbstractJsonController
{
    /**
     * Fetch all members from squad id
     * @return array
     */
    public function fetchAll(){

        $squadId = $this->params('id', 0);

        if( $squadId <= 0 )
        {
            return new ApiResponse(null, 'missing squad parameter id', 404);
        }

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $userSquad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => $squadId));

        if( ! $userSquad )
        {
            return new ApiResponse(null, 'squad not found', 404);
        }

        $result = [];
        foreach($userSquad->getMembers() as $squadMember)
        {
            if( $squadMember instanceof ArraySerializableInterface )
                $result[] = $squadMember->getArrayCopy();
        }

        return new ApiResponse($result, null, 200);
    }

    /**
     * Fetch specific member from squad
     * @return array
     */
    public function fetch(){

        $squadId = $this->params('id', 0);
        $memberId = $this->params('mid', false);

        if( $squadId <= 0 )
        {
            return new ApiResponse(null, 'missing squad parameter id', 404);
        }
        if( $memberId == false )
        {
            return new ApiResponse(null, 'missing member parameter id', 404);
        }

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => $squadId), array('id' => 'desc'));

        if( !$squad )
        {
            return new ApiResponse(null, 'squad not found', 404);
        }

        $memberRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Member');
        $member = $memberRepository->findOneBy(array('squad' => $squad, 'uuid' => $memberId));

        if( !$member )
        {
            return new ApiResponse(null, 'member not found', 404);
        }

        if( $member instanceof ArraySerializableInterface )
        {
                $result = $member->getArrayCopy();
        }

        return new ApiResponse($result, null, 200);
    }

    /**
     * Update a member from squad
     *
     * @return ApiResponse
     */
    public function update()
    {
        $squadId = $this->params('id', 0);
        $memberId = $this->params('mid', false);

        if( $squadId <= 0 )
        {
            return new ApiResponse(null, 'missing squad parameter id', 404);
        }
        if( $memberId == false )
        {
            return new ApiResponse(null, 'missing member parameter id', 404);
        }

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        /** @var \Frontend\Squads\Entity\Squad $squad */
        $squad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => $squadId), array('id' => 'desc'));

        if( !$squad )
        {
            return new ApiResponse(null, 'squad not found', 404);
        }

        $memberRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Member');
        /** @var \Frontend\Squads\Entity\Member $member */
        $member = $memberRepository->findOneBy(array('squad' => $squad, 'uuid' => $memberId));

        if( !$member )
        {
            return new ApiResponse(null, 'member not found', 404);
        }

        $postData = $this->getArrayPostData();
        $memberData = $member->getArrayCopy();

        $postData = array_merge($memberData,$postData);

        $memberForm = new Member();
        $memberForm->setEntityManager($this->getEntityManager());
        $memberForm->init($member);
        $memberForm->setData($postData);

        if( $memberForm->isValid() )
        {
            /** @var \Frontend\Squads\Entity\Member $member */
            $member = $memberForm->getData();

            $this->getEntityManager()->merge($member);
            $this->getEntityManager()->flush($member);

            // save
            return new ApiResponse($member->getArrayCopy(), null, 200);

        } else {

            return new ApiResponse(null, $memberForm->getMessages(), 422);
        }
    }

    /**
     * Delete a squad member
     * @return ApiResponse
     */
    public function delete()
    {
        $squadId = $this->params('id', 0);
        $memberId = $this->params('mid', false);

        if( $squadId <= 0 )
        {
            return new ApiResponse(null, 'missing squad parameter id', 404);
        }
        if( $memberId == false )
        {
            return new ApiResponse(null, 'missing member parameter id', 404);
        }

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        $squad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => $squadId), array('id' => 'desc'));

        if( !$squad )
        {
            return new ApiResponse(null, 'squad not found', 404);
        }

        $memberRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Member');
        $member = $memberRepository->findOneBy(array('squad' => $squad, 'uuid' => $memberId));

        if( !$member )
        {
            return new ApiResponse(null, 'member not found', 404);
        }

        $this->getEntityManager()->remove($member);
        $this->getEntityManager()->flush();

        return new ApiResponse(null, null, 200);
    }

    /**
     * Create a new Squad Member
     *
     * @return ApiResponse
     */
    public function create()
    {
        $squadId = $this->params('id', 0);

        if( $squadId <= 0 )
        {
            return new ApiResponse(null, 'missing squad parameter id', 404);
        }

        $squadRepository = $this->getEntityManager()->getRepository('Frontend\Squads\Entity\Squad');
        /** @var \Frontend\Squads\Entity\Squad $squad */
        $squad = $squadRepository->findOneBy(array('user' => $this->getApiIdentity(), 'id' => $squadId), array('id' => 'desc'));

        if( !$squad )
        {
            return new ApiResponse(null, 'squad not found', 404);
        }

        $postData = $this->getArrayPostData();

        $memberForm = new Member();
        $memberForm->setEntityManager($this->getEntityManager());
        $memberForm->init(new \Frontend\Squads\Entity\Member());
        $memberForm->setData($postData);

        if( $memberForm->isValid() )
        {
            /** @var \Frontend\Squads\Entity\Member $member */
            $member = $memberForm->getData();
            $member->setSquad($squad);

            Try {
                $this->getEntityManager()->persist($member);
                $this->getEntityManager()->flush($member);
            } Catch(\Exception $e)
            {
                /** @todo Member UUID + Username allready exists validator */
                return new ApiResponse(null, array('uuid' => 'allready exists', 'username' => 'allready exists'), 422);
            }
            // save
            return new ApiResponse($member->getArrayCopy(), null, 201);

        } else {

            return new ApiResponse(null, $memberForm->getMessages(), 422);
        }
    }
}
