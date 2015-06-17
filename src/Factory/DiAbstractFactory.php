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
        $config = (array)$serviceLocator->get('config')['mxdimodule'];
        $knownServices = $config['avoid_service'];

        if (isset($knownServices[$name]) && $knownServices[$name]) {
            // avoid known services
            return false;
        }

        if (!$this->cache) {
            $this->cache = StorageFactory::adapterFactory($config['cache_adapter'], $config['cache_options']);
            if (is_callable([$this->cache, ['addPlugin']])) {
                $this->cache->addPlugin(new Serializer());
            }
        }

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
}
