<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use Zend\ServiceManager\ServiceLocatorInterface;

class Instantiator
{
    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    public function __construct(ServiceLocatorInterface $sm)
    {
        $this->serviceLocator = $sm;
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
        if ($changeSet->hasSimpleConstructor()) {
            $object = new $fqcn;
        } else {
            $reflection = new \ReflectionClass($fqcn);
            $object = $reflection->newInstanceArgs(
                $changeSet->getConstructorInjections()->getValue($this->serviceLocator)
            );
        }

        /**
         * @var string $methodName
         * @var InjectParams $injection
         */
        foreach ($changeSet->getMethodsInjections() as $methodName => $injection) {
            if (is_callable([$object, $methodName])) {
                call_user_func_array([$object, $methodName], $injection->getValue($this->serviceLocator));
                continue;
            }

            $reflection = new \ReflectionMethod($fqcn, $methodName);
            $reflection->setAccessible(true);
            $reflection->invokeArgs($object, $injection->getValue($this->serviceLocator));
            $reflection->setAccessible(false);
        }

        /**
         * @var string $propertyName
         * @var Inject $injection
         */
        foreach ($changeSet->getPropertiesInjections() as $propertyName => $injection) {
            $value = $injection->getValue($this->serviceLocator);

            if (isset(get_object_vars($object)[$propertyName])) {
                $object->$propertyName = $value;
                continue;
            }

            $reflection = new \ReflectionProperty($fqcn, $propertyName);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $value);
            $reflection->setAccessible(false);
        }

        return $object;
    }
}
