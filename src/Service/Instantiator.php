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
     * @param InjectParams $ci Constructor injection params
     * @param InjectParams[] $mi Methods injections (method name => injection params)
     * @param Inject[] $pi Properties injections (property name => injection params)
     * @return object
     */
    public function create($fqcn, InjectParams $ci = null, array $mi = [], array $pi = [])
    {
        if ($ci) {
            $reflection = new \ReflectionClass($fqcn);
            $object = $reflection->newInstanceArgs($ci->getValue($this->serviceLocator));
        } else {
            $object = new $fqcn;
        }

        /**
         * @var string $methodName
         * @var InjectParams $injection
         */
        foreach ($mi as $methodName => $injection) {
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
        foreach ($pi as $propertyName => $injection) {
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
