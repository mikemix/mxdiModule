<?php
namespace mxdiModule\Factory;

use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiAbstractFactory implements AbstractFactoryInterface
{
    /** @var array */
    protected $config;

    /** @var ChangeSet|mixed */
    protected $changeSet;

    /** @var AnnotationExtractor */
    protected $extractor;

    /** @var Instantiator */
    protected $instantiator;

    /** @var StorageInterface|AbstractAdapter */
    protected $cache;

    public function __construct(AnnotationExtractor $extractor = null, Instantiator $instantiator = null)
    {
        $this->extractor = $extractor ?: new AnnotationExtractor();
        $this->instantiator = $instantiator ?: new Instantiator();
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (! $this->config) {
            $this->config = (array)$serviceLocator->get('config')['mxdimodule'];
        }

        if (isset($this->config['avoid_service'][$name]) && $this->config['avoid_service'][$name]) {
            // avoid known services
            return false;
        }

        $this->changeSet = $this->getCache()->getItem($name);

        if ($this->changeSet instanceof ChangeSet) {
            // Positive result available via cache
            // Because we don't allow not annotated results to be set there
            return true;
        }

        // Result is not available via cache
        // Calculate the result first
        $this->changeSet = $this->extractor->getChangeSet($requestedName);

        if ($this->changeSet->isAnnotated()) {
            // Service is annotated to cache results
            $this->getCache()->setItem($name, $this->changeSet);
            return true;
        }

        // Service is not annotated
        // Cache false for it
        $this->getCache()->setItem($name, false);
        return false;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return object
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $this->instantiator->setServiceLocator($serviceLocator);
        return $this->instantiator->create($requestedName, $this->changeSet);
    }

    /**
     * Get cache adapter
     *
     * @return AbstractAdapter|StorageInterface
     */
    protected function getCache()
    {
        if ($this->cache) {
            return $this->cache;
        }

        $this->cache = StorageFactory::adapterFactory(
            $this->config['cache_adapter'],
            $this->config['cache_options']
        );

        if ($this->cache instanceof AbstractAdapter) {
            $this->cache->addPlugin(new Serializer());
        }

        return $this->cache;
    }
}
