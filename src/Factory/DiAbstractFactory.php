<?php
namespace mxdiModule\Factory;

use mxdiModule\Service\ExtractorInterface;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;

class DiAbstractFactory implements AbstractFactoryInterface
{
    /** @var array */
    protected $config;

    /** @var ChangeSet|mixed */
    protected $changeSet;

    /** @var ExtractorInterface */
    protected $extractor;

    /** @var Instantiator */
    protected $instantiator;

    /** @var StorageInterface|AbstractAdapter */
    protected $cache;

    public function __construct(Instantiator $instantiator = null)
    {
        $this->setInstantiator($instantiator ?: new Instantiator());
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
        $this->setChangeSet($this->cache->getItem($name));

        if ($this->getChangeSet() instanceof ChangeSet) {
            // Positive result available via cache
            // Because we don't allow not annotated results to be set there
            return true;
        }

        // Result is not available via cache
        // Calculate the result first
        $this->initializeExtractor($serviceLocator);
        $this->setChangeSet($this->extractor->getChangeSet($requestedName));

        if ($this->getChangeSet()->isAnnotated()) {
            // Service is annotated to cache results
            $this->cache->setItem($name, $this->getChangeSet());
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
        return $this->instantiator->create($requestedName, $this->getChangeSet());
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
    protected function initializeExtractor(ServiceLocatorInterface $serviceLocator)
    {
        if (!$this->extractor) {
            $this->extractor = $serviceLocator->get('mxdiModule\Extractor');
        }
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

    /**
     * @return mixed|ChangeSet
     */
    public function getChangeSet()
    {
        return $this->changeSet;
    }

    /**
     * @param mixed|ChangeSet $changeSet
     */
    public function setChangeSet($changeSet)
    {
        $this->changeSet = $changeSet;
    }

    /**
     * @param ExtractorInterface $extractor
     */
    public function setExtractor(ExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @param Instantiator $instantiator
     */
    public function setInstantiator(Instantiator $instantiator)
    {
        $this->instantiator = $instantiator;
    }
}
