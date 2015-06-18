<?php
namespace mxdiModule\Factory\Controller;

use mxdiModule\Controller\ProxyClearController;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProxyClearControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ProxyClearController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return new ProxyClearController($serviceLocator->get('config')['mxdimodule']['proxy_dir']);
    }
}
