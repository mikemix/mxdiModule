<?php
namespace mxdiModule\Annotation;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class Inject
{
    /** @var string */
    public $value;

    /** @var bool */
    public $invokable = false;

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->value;
    }

    /**
     * @param ServiceLocatorInterface|null $sm
     * @return object
     */
    public function getObject(ServiceLocatorInterface $sm = null)
    {
        $serviceName = $this->getServiceName();

        if ($this->isInvokable()) {
            return new $serviceName;
        }

        return $sm->get($serviceName);
    }

    /**
     * @return bool
     */
    public function isInvokable()
    {
        return $this->invokable;
    }
}
