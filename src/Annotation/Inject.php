<?php
namespace mxdiModule\Annotation;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class Inject implements Annotation
{
    /** @var string */
    public $value;

    /** @var bool */
    public $invokable = false;

    /**
     * @param ServiceLocatorInterface|null $sm
     * @return object
     */
    public function getValue(ServiceLocatorInterface $sm = null)
    {
        $serviceName = $this->value;

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
