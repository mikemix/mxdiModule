<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\Inject;
use Zend\ServiceManager\ServiceLocatorInterface;

class Instantiator
{
    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Create object.
     *
     * @param string $fqcn
     * @param ChangeSet $changeSet
     * @return object
     */
    public function create($fqcn, ChangeSet $changeSet)
    {
        if (!$this->serviceLocator) {
            throw new \InvalidArgumentException('Service locator is mandatory');
        }

        $object = $this->createObject($fqcn, $changeSet);
        $this->injectMethods($object, $fqcn, $changeSet);
        $this->injectProperties($object, $fqcn, $changeSet);

        return $object;
    }

    /**
     * @param object $object
     * @param string $fqcn
     * @param ChangeSet $changeSet
     */
    protected function injectProperties($object, $fqcn, ChangeSet $changeSet)
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

            $this->setPropertyValue($fqcn, $object, $propertyName, $value);
        }
    }

    /**
     * @param object $object
     * @param string $fqcn
     * @param ChangeSet $changeSet
     */
    protected function injectMethods($object, $fqcn, ChangeSet $changeSet)
    {
        /**
         * @var string $propertyName
         * @var Inject $injection
         * @var array $value
         */
        foreach ($changeSet->getMethodsInjections() as $methodName => $injection) {
            $value = $injection->getValue($this->serviceLocator);

            if ($changeSet->isMethodPublic($methodName)) {
                call_user_func_array([$object, $methodName], $value);
                continue;
            }

            $this->invokeMethod($fqcn, $object, $methodName, $value);
        }
    }

    /**
     * @param string $fqcn
     * @param ChangeSet $changeSet
     * @return object
     */
    protected function createObject($fqcn, ChangeSet $changeSet)
    {
        if ($changeSet->hasSimpleConstructor()) {
            return new $fqcn;
        }

        $reflection = new \ReflectionClass($fqcn);
        return $reflection->newInstanceArgs(
            $changeSet->getConstructorInjections()->getValue($this->serviceLocator)
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
