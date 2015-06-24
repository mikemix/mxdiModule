<?php
namespace mxdiModule\Factory\Controller;

use mxdiModule\Controller\CacheClearController;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheClearControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return CacheClearController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var StorageInterface $storage */
        $storage = $serviceLocator->get('mxdiModule\Cache');

        return new CacheClearController($storage);
    }
}
