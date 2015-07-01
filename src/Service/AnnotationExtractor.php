<?php
namespace mxdiModule\Service;

use Doctrine\Common\Annotations\AnnotationReader as Reader;
use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;

class AnnotationExtractor
{
    /** @var Reader */
    protected $reader;

    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader ?: new Reader();
    }

    /**
     * @param string $fqcn
     * @return InjectParams|null
     */
    public function getConstructorInjections($fqcn)
    {
        if (! in_array('__construct', (array)get_class_methods($fqcn))) {
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
     * @return InjectParams[]
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

            $reflectionMethod = new \ReflectionMethod($fqcn, $name);
            $inject = $this->reader->getMethodAnnotation($reflectionMethod, AnnotationInterface::class);

            if (null !== $inject) {
                $injections[$name] = [
                    'public'    => $reflectionMethod->isPublic(),
                    'inject' => $inject,
                ];
            }
        }

        return $injections;
    }

    /**
     * Get properties injections (except the constructor).
     *
     * @param string $fqcn
     * @return Inject[]
     */
    public function getPropertiesInjections($fqcn)
    {
        $injections = [];
        $reflection = new \ReflectionClass($fqcn);

        foreach ($reflection->getProperties() as $property) {
            $reflectionProperty = new \ReflectionProperty($fqcn, $property->getName());
            $inject = $this->reader->getPropertyAnnotation($reflectionProperty, AnnotationInterface::class);

            if (null !== $inject) {
                $injections[$property->getName()] = [
                    'public' => $reflectionProperty->isPublic(),
                    'inject' => $inject,
                ];
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
