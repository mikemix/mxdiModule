<?php
namespace mxdiModule\ServiceManager;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\Instantiator;

/**
 * @TODO DELETE THIS FILE BECAUSE IT IS NOT NEEDED
 * DEFAULT FACTORY WORKS FOR ABSTRACT MANAGERS AS WELL
 */
class DiAbstractPluginFactory implements AbstractFactoryInterface
{
    /** @var DiAbstractFactory */
    protected $factory;

    public function __construct(DiAbstractFactory $factory = null)
    {
        $this->factory = $factory ?: new DiAbstractFactory(new AnnotationExtractor(), new Instantiator());
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
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this->factory->canCreateServiceWithName($serviceLocator, $name, $requestedName);
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
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this->factory->createServiceWithName($serviceLocator, $name, $requestedName);
    }
}
