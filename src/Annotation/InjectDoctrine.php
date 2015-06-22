<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class InjectDoctrine implements AnnotationInterface
{
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
        $name = 'Doctrine\ORM\EntityManager';

        try {
            return $sm->get($name);
        } catch (\Exception $e) {
            throw CannotGetValue::of($name);
        }
    }
}
