<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use mxdiModule\Factory\ProxyFactory;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class InjectLazy implements Annotation
{
    /** @var string */
    public $value;

    /** @var string */
    public $fqcn;

    /** @var LazyLoadingValueHolderFactory */
    protected $factory;

    /**
     * Get the value.
     *
     * @param ServiceLocatorInterface|null $sm
     * @return object
     *
     * @throws CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm)
    {
        $serviceName = $this->value;
        $fqcn = $this->fqcn ?: $serviceName;

        $initializer = function (& $object, LazyLoadingInterface $proxy) use ($sm, $serviceName) {
            try {
                $object = $sm->get($serviceName);
            } catch (\Exception $e) {
                return false;
            }

            $proxy->setProxyInitializer(null);
            return true;
        };

        return $this->getFactory($sm)->createProxy($fqcn, $initializer);
    }

    /**
     * @param ServiceLocatorInterface $sm
     * @return LazyLoadingValueHolderFactory
     */
    public function getFactory(ServiceLocatorInterface $sm)
    {
        if (! $this->factory) {
            /** @var LazyLoadingValueHolderFactory $factory */
            $factory = $sm->get(ProxyFactory::class);
            $this->setFactory($factory);
        }

        return $this->factory;
    }

    /**
     * @param LazyLoadingValueHolderFactory $factory
     */
    public function setFactory(LazyLoadingValueHolderFactory $factory)
    {
        $this->factory = $factory;
    }
}
