<?php
namespace mxdiModule\Factory;

use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;

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
        if ($this->shouldBeAvoided($serviceLocator, $name)) {
            return false;
        }

        $this->initializeCache($serviceLocator);
        $this->changeSet = $this->cache->getItem($name);

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
            $this->cache->setItem($name, $this->changeSet);
            return true;
        }

        // Service is not annotated
        // Cache false for it
        $this->cache->setItem($name, false);
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
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @return bool
     */
    protected function shouldBeAvoided(ServiceLocatorInterface $serviceLocator, $name)
    {
        if (! $this->config) {
            $this->config = $serviceLocator->get('config')['mxdimodule']['avoid_service'];
        }

        return isset($this->config[$name]) && $this->config[$name];
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function initializeCache(ServiceLocatorInterface $serviceLocator)
    {
        if (!$this->cache) {
            $this->cache = $serviceLocator->get('mxdiModule\Cache');
        }
    }
}
