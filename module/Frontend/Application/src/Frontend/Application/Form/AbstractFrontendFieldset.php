<?php
namespace Frontend\Application\Form;

use Zend\Form\Fieldset;
use Zend\ServiceManager\ServiceManager;

abstract class AbstractFrontendFieldset extends Fieldset {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /** @var  \Zend\ServiceManager\ServiceManagerAwareInterface */
    protected $serviceManager;

    /**
     * Liefert den EntityManager
     *
     * @author  Marco Rieger
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        }
        return $this->entityManager;
    }

    /**
     * Setzt den Entity Manager
     *
     * @author  Marco Rieger
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @return $this
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Setzt den Service Manager
     *
     * @author  Marco Rieger
     * @param ServiceManager $serviceManager
     * @return $this
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Liefert den Service Manager
     *
     * @author  Marco Rieger
     * @return mixed
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

}