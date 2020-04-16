<?php
namespace Frontend\Squads\Repository;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Zend\Math\Rand;

class Squad extends DoctrineEntityRepository
{
    /**
     * Create an unique token for a Squad
     *
     * @param \Frontend\Squads\Entity\Squad $squad
     * @return bool
     */
    public function createUniqueToken( \Frontend\Squads\Entity\Squad &$squad )
    {
        $chars = array_merge(
            range(0,9),
            range('A', 'Z'),
            range('a', 'z')
        );

        $token = Rand::getString(32, implode('',$chars), true);

        if( $this->checkUniqueTokenExists($token) )
        {
            $this->createUniqueToken( $squad );
            return true;
        }

        $squad->setPrivateID( $token );
        return true;
    }

    /**
     * Checks if a token is allready used
     *
     * @param $token
     * @return bool
     */
    private function checkUniqueTokenExists( $token )
    {
        return $this->findOneBy(array('privateID' => $token)) ? true : false;
    }

}