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
        $reflection = new \ReflectionClass($fqcn);
        $object = $reflection->newInstanceArgs($this->convertParamsToArray($ci));

        /**
         * @var string $methodName
         * @var InjectParams $injection
         */
        foreach ($mi as $methodName => $injection) {
            $reflection = new \ReflectionMethod($fqcn, $methodName);

            if (!$reflection->isPublic()) {
                $reflection->setAccessible(true);
            }

            $reflection->invokeArgs($object, $this->convertParamsToArray($injection));

            if (!$reflection->isPublic()) {
                $reflection->setAccessible(false);
            }
        }

        /**
         * @var string $propertyName
         * @var Inject $injection
         */
        foreach ($pi as $propertyName => $injection) {
            $reflection = new \ReflectionProperty($fqcn, $propertyName);

            $serviceName = $injection->getServiceName();
            $value = $injection->isInvokable() ? new $serviceName : $this->serviceLocator->get($serviceName);

            if (!$reflection->isPublic()) {
                $reflection->setAccessible(true);
                $reflection->setValue($object, $value);
                $reflection->setAccessible(false);
            } else {
                $object->$propertyName = $value;
            }
        }

        return $object;
    }

    /**
     * Converts injectParams object into array of parameters.
     *
     * @param InjectParams $injections
     * @return array
     */
    protected function convertParamsToArray(InjectParams $injections)
    {
        $params = [];
        foreach ($injections->getInjections() as $param) {
            $serviceName = $param->getServiceName();
            $value = $param->isInvokable() ? new $serviceName : $this->serviceLocator->get($serviceName);

            $params[] = $value;
        }

        return $params;
    }
}
