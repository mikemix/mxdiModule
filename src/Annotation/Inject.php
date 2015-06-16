<?php
namespace mxdiModule\Annotation;

use mxdiModule\Annotation\Exception\CannotGetValue;
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
     *
     * @throws Exception\CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm = null)
    {
        $serviceName = $this->value;

        if ($this->isInvokable()) {
            return new $serviceName;
        }

        if (! $sm) {
            throw CannotGetValue::serviceManagerMissing();
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
