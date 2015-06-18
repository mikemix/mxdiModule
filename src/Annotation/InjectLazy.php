<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
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

        return $this->getFactory()->createProxy($fqcn, $initializer);
    }

    /**
     * @return LazyLoadingValueHolderFactory
     */
    public function getFactory()
    {
        if (! $this->factory) {
            $this->setFactory(new LazyLoadingValueHolderFactory());
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
