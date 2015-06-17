<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
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
     * @throws CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm)
    {
        $serviceName = $this->value;

        if ($this->invokable) {
            return new $serviceName;
        }

        try {
            return $sm->get($serviceName);
        } catch (\Exception $e) {
            throw CannotGetValue::of($this->value);
        }
    }
}
