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

        if ($changeSet->hasSimpleConstructor()) {
            $object = new $fqcn;
        } else {
            $reflection = new \ReflectionClass($fqcn);
            $object = $reflection->newInstanceArgs(
                $changeSet->getConstructorInjections()->getValue($this->serviceLocator)
            );
        }

        /**
         * @var string $propertyName
         * @var Inject[]|bool[] $injection
         */
        foreach ($changeSet->getMethodsInjections() as $methodName => $injection) {
            $value = $injection['inject']->getValue($this->serviceLocator);

            if ($injection['public']) {
                call_user_func_array([$object, $methodName], $value);
                continue;
            }

            $this->invokeMethod($fqcn, $object, $methodName, $value);
        }

        /**
         * @var string $propertyName
         * @var Inject[]|bool[] $injection
         */
        foreach ($changeSet->getPropertiesInjections() as $propertyName => $injection) {
            $value = $injection['inject']->getValue($this->serviceLocator);

            if ($injection['public']) {
                $object->$propertyName = $value;
                continue;
            }

            $this->setPropertyValue($fqcn, $object, $propertyName, $value);
        }

        return $object;
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
