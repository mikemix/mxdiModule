<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\Inject;
use Zend\ServiceManager\ServiceLocatorInterface;

class Instantiator implements InstantiatorInterface
{
    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    /**
     * Create object.
     *
     * @param ServiceLocatorInterface $sm
     * @param ChangeSet $changeSet
     * @return object
     */
    public function create(ServiceLocatorInterface $sm, ChangeSet $changeSet)
    {
        $this->serviceLocator = $sm;

        $object = $this->createObject($changeSet);
        $this->injectMethods($object, $changeSet);
        $this->injectProperties($object, $changeSet);

        return $object;
    }

    /**
     * @param object $object
     * @param ChangeSet $changeSet
     */
    protected function injectProperties($object, ChangeSet $changeSet)
    {
        /**
         * @var string $propertyName
         * @var Inject $injection
         */
        foreach ($changeSet->getPropertiesInjections() as $propertyName => $injection) {
            $value = $injection->getValue($this->serviceLocator);

            if ($changeSet->isPropertyPublic($propertyName)) {
                $object->$propertyName = $value;
                continue;
            }

            $this->setPropertyValue($changeSet->getFqcn(), $object, $propertyName, $value);
        }
    }

    /**
     * @param object $object
     * @param ChangeSet $changeSet
     */
    protected function injectMethods($object, ChangeSet $changeSet)
    {
        /**
         * @var string $propertyName
         * @var Inject $injection
         * @var array $value
         */
        foreach ($changeSet->getMethodsInjections() as $methodName => $injection) {
            $value = $injection->getValue($this->serviceLocator);

            if ($changeSet->isMethodPublic($methodName)) {
                call_user_func_array([$object, $methodName], (array)$value);
                continue;
            }

            $this->invokeMethod($changeSet->getFqcn(), $object, $methodName, $value);
        }
    }

    /**
     * @param ChangeSet $changeSet
     * @return object
     */
    protected function createObject(ChangeSet $changeSet)
    {
        $fqcn = $changeSet->getFqcn();

        if ($changeSet->hasSimpleConstructor()) {
            return new $fqcn;
        }

        $reflection = new \ReflectionClass($fqcn);
        return $reflection->newInstanceArgs(
            (array)$changeSet->getConstructorInjections()->getValue($this->serviceLocator)
        );
    }

    /**
     * Set property value with reflection.
     *
     * @param string $fqcn
     * @param object $object
     * @param string $property
     * @param mixed $value
     */
    protected function setPropertyValue($fqcn, $object, $property, $value)
    {
        $reflection = new \ReflectionProperty($fqcn, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
        $reflection->setAccessible(false);
    }

    /**
     * Call method with reflection.
     *
     * @param string $fqcn
     * @param object $object
     * @param string $method
     * @param mixed $value
     */
    protected function invokeMethod($fqcn, $object, $method, $value)
    {
        $reflection = new \ReflectionMethod($fqcn, $method);
        $reflection->setAccessible(true);
        $reflection->invokeArgs($object, $value);
        $reflection->setAccessible(false);
    }
}
