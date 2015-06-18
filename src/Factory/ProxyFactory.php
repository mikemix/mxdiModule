<?php
namespace mxdiModule\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Configuration;

class ProxyFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Configuration
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')['mxdimodule'];

        $configuration = new Configuration();
        $configuration->setProxiesNamespace($config['proxy_namespace']);
        $configuration->setProxiesTargetDir($config['proxy_dir']);

        spl_autoload_register($configuration->getProxyAutoloader());

        return new LazyLoadingValueHolderFactory($configuration);
    }
}
