<?php
namespace Auth\Repository;

use Application\Repository\BaseEntityRepository;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class Benutzer extends BaseEntityRepository
{

    /**
     * Liefert alle Benutzer
     *
     * @return array|\Doctrine\ORM\Query
     */
    public function findALl() {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t0');
        $queryBuilder->from('Auth\Entity\Benutzer', 't0');

        $query = $queryBuilder->getQuery();

        $this->setCurrentQuery( $query );
        $this->setCurrentQueryBuilder( $queryBuilder );

        return $query;
    }




}