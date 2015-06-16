<?php
namespace mxdiModule\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;

class AnnotationExtractor
{
    /** @var AnnotationReader */
    protected $reader;

    public function __construct(AnnotationReader $reader = null)
    {
        $this->reader = $reader ?: new AnnotationReader();
    }

    /**
     * @param string $fqcn
     * @return \mxdiModule\Annotation\InjectParams|null
     */
    public function getConstructorInjections($fqcn)
    {
        if (! in_array('__construct', get_class_methods($fqcn))) {
            return null;
        }

        return $this->reader->getMethodAnnotation(
            new \ReflectionMethod($fqcn, '__construct'),
            InjectParams::class
        );
    }

    /**
     * Get methods injections (except the constructor).
     *
     * @param string $fqcn
     * @return \mxdiModule\Annotation\InjectParams[]
     */
    public function getMethodsInjections($fqcn)
    {
        $injections = [];

        $reflection = new \ReflectionClass($fqcn);

        foreach ($reflection->getMethods() as $method) {
            $name = $method->getName();
            if ('__construct' === $name) {
                continue;
            }

            $inject = $this->reader->getMethodAnnotation(
                new \ReflectionMethod($fqcn, $name),
                InjectParams::class
            );

            if (null !== $inject) {
                $injections[$name] = $inject;
            }
        }

        return $injections;
    }

    /**
     * Get properties injections (except the constructor).
     *
     * @param string $fqcn
     * @return \mxdiModule\Annotation\Inject[]
     */
    public function getPropertiesInjections($fqcn)
    {
        $injections = [];
        $reflection = new \ReflectionClass($fqcn);

        foreach ($reflection->getProperties() as $property) {
            $inject = $this->reader->getPropertyAnnotation(
                new \ReflectionProperty($fqcn, $property->getName()),
                Inject::class
            );

            if (null !== $inject) {
                $injections[$property->getName()] = $inject;
            }
        }

        return $injections;
    }

    /**
     * Handy shortcut.
     *
     * @param string $fqcn
     * @return ChangeSet
     */
    public function getChangeSet($fqcn)
    {
        return new ChangeSet($this, $fqcn);
    }
}
