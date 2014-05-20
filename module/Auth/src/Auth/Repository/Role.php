<?php
namespace Auth\Repository;

use Application\Repository\BaseEntityRepository;

class Role extends BaseEntityRepository
{

    /**
     * Liefert alle Rollen
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function findALl() {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t0');
        $queryBuilder->from('Auth\Entity\Role', 't0');

        $query = $queryBuilder->getQuery();

        $this->setCurrentQuery( $query );
        $this->setCurrentQueryBuilder( $queryBuilder );

        return $query;
    }




}