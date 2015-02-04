<?php
namespace Application\Service;

use Racecore\GATracking\GATracking;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnalyticsServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return GATracking
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['analytics'];
        return new GATracking($config['account_id'], $config);
    }
}