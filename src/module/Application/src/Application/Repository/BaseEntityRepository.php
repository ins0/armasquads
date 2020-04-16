<?php
namespace Application\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

abstract class BaseEntityRepository extends DoctrineEntityRepository {

    /**
     * @var AbstractQuery Aktueller Query
     */
    protected $currentQuery = null;

    /**
     * @var QueryBuilder
     */
    protected $currentQueryBuilder = null;

    /**
     * @param \Doctrine\ORM\QueryBuilder $currentQueryBuilder
     */
    public function setCurrentQueryBuilder($currentQueryBuilder)
    {
        $this->currentQueryBuilder = $currentQueryBuilder;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCurrentQueryBuilder()
    {
        return $this->currentQueryBuilder;
    }

    /**
     * @param \Doctrine\ORM\AbstractQuery $currentQuery
     */
    public function setCurrentQuery($currentQuery)
    {
        $this->currentQuery = $currentQuery;
    }

    /**
     * @return \Doctrine\ORM\AbstractQuery
     */
    public function getCurrentQuery()
    {
        return $this->currentQuery;
    }

    /**
     * Liefert die aktuellen Results
     *
     * @return array
     */
    public function getResults() {

        return $this->currentQuery->getResult();
    }

    /**
     * Get Paginator
     *
     * @author  Marco Rieger
     *
     * @param QueryBuilder $qb
     * @param bool         $fetchJoinCollection
     *
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator( AbstractQuery $qb, $seite = 1, $fetchJoinCollection = false)
    {

        $adapter = new \DoctrineORMModule\Paginator\Adapter\DoctrinePaginator(
            new \Doctrine\ORM\Tools\Pagination\Paginator($qb, $fetchJoinCollection)
        );
        $paginator = new \Zend\Paginator\Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(1);

        $seite = (int)$seite;
        if ($seite <= 1) {
            $paginator->setCurrentPageNumber(1);
        } else {
            $paginator->setCurrentPageNumber($seite);
        }


        return $paginator;
    }

    /**
     * Speichert ein Entity
     *
     * @param $data
     */
    public function save( $data ) {

        $this->getEntityManager()->merge( $data );
        $this->getEntityManager()->flush();

        return UnitOfWork::STATE_MANAGED === $this->getEntityManager()->getUnitOfWork()->getEntityState( $data );
    }

}