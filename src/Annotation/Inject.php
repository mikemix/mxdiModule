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
     * Get the value.
     *
     * @param ServiceLocatorInterface|null $sm
     * @return object
     */
    public function getValue(ServiceLocatorInterface $sm)
    {
        $serviceName = $this->value;

        if ($this->invokable) {
            return new $serviceName;
        }

        return $sm->get($serviceName);
    }
}
