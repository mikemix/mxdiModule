<?php
namespace mxdiModule\Factory;

use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\Instantiator;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiFactory implements AbstractFactoryInterface
{
    protected $constructorInjections;
    protected $methodsInjections = [];
    protected $propertiesInjections = [];

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
        $extractor = new AnnotationExtractor();

        $this->constructorInjections = $extractor->getConstructorInjections($requestedName);
        $this->methodsInjections = $extractor->getMethodsInjections($requestedName);
        $this->propertiesInjections = $extractor->getPropertiesInjections($requestedName);

        return
            $this->constructorInjections ||
            count($this->methodsInjections) ||
            count($this->propertiesInjections);
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

        return $instantiator->create(
            $requestedName,
            $this->constructorInjections,
            $this->methodsInjections,
            $this->propertiesInjections
        );
    }
}
