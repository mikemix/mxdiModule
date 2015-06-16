<?php
namespace mxdiModule\Factory;

use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiAbstractFactory implements AbstractFactoryInterface
{
    /** @var ChangeSet */
    protected $changeSet;

    /** @var AnnotationExtractor */
    protected $extractor;

    public function __construct(AnnotationExtractor $extractor = null)
    {
        $this->extractor = $extractor ?: new AnnotationExtractor();
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
        $cache = StorageFactory::adapterFactory($config['cache_adapter'], $config['cache_options']);

        if ($cache->hasItem($name)) {
            $this->changeSet = $cache->getItem($name);
        } else {
            $this->changeSet = $this->extractor->getChangeSet($requestedName);
            $cache->setItem($name, $this->changeSet);
        }

        return $this->changeSet->isAnnotated();
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
        $instantiator = new Instantiator($serviceLocator);
        return $instantiator->create($requestedName, $this->changeSet);
    }
}
