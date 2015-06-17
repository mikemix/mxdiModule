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
    /** @var ChangeSet */
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

        if ($this->cache->hasItem($name)) {
            $this->changeSet = $this->cache->getItem($name);
        } else {
            $this->changeSet = $this->extractor->getChangeSet($requestedName);
            $this->changeSet = $this->changeSet->isAnnotated() ? $this->changeSet : false;

            $this->cache->setItem($name, $this->changeSet);
        }

        return $this->changeSet && $this->changeSet->isAnnotated();
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
     * @param ChangeSet $changeSet
     */
    public function setChangeSet(ChangeSet $changeSet)
    {
        $this->changeSet = $changeSet;
    }
}
