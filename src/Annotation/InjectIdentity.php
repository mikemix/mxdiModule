<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class InjectIdentity implements AnnotationInterface
{
    /**
     * Get the value.
     *
     * @param ServiceLocatorInterface $sm
     * @return object|null
     *
     * @throws CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm)
    {
        try {
            return $sm->get('Zend\Authentication\AuthenticationService')->getIdentity();
        } catch (\Exception $e) {
            throw CannotGetValue::of('identity', $e);
        }
    }
}
