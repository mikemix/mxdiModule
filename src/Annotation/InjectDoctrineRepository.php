<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class InjectDoctrineRepository implements AnnotationInterface
{
    /**
     * FQCN of the entity
     * @var string
     */
    public $value;

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
        try {
            return $sm->get('Doctrine\ORM\EntityManager')->getRepository($this->value);
        } catch (\Exception $e) {
            throw CannotGetValue::of(sprintf('repository of %s', $this->value), $e);
        }
    }
}
