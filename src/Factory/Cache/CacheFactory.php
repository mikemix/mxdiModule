<?php
namespace mxdiModule\Factory\Cache;

use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return StorageInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config')['mxdimodule'];

        $cache = StorageFactory::adapterFactory(
            $config['cache_adapter'],
            $config['cache_options']
        );

        if ($cache instanceof AbstractAdapter) {
            $cache->addPlugin(new Serializer());
        }

        return $cache;
    }
}
