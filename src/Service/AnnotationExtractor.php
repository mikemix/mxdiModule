<?php
namespace mxdiModule\Service;

use Doctrine\Common\Annotations\AnnotationReader as Reader;
use mxdiModule\Annotation\AnnotationInterface;

class AnnotationExtractor implements ExtractorInterface
{
    /** @var Reader */
    protected $reader;

    public function __construct()
    {
        $this->reader = new Reader();
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructorInjections($fqcn)
    {
        if (! in_array('__construct', (array)get_class_methods($fqcn))) {
            return null;
        }

        return $this->reader->getMethodAnnotation(
            new \ReflectionMethod($fqcn, '__construct'),
            AnnotationInterface::class
        );
    }

    /**
     * {@inheritdoc}
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
                AnnotationInterface::class
            );

            if (null !== $inject) {
                $injections[$name] = $inject;
            }
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesInjections($fqcn)
    {
        $injections = [];
        $reflection = new \ReflectionClass($fqcn);

        foreach ($reflection->getProperties() as $property) {
            $reflectionProperty = new \ReflectionProperty($fqcn, $property->getName());
            $inject = $this->reader->getPropertyAnnotation($reflectionProperty, AnnotationInterface::class);

            if (null !== $inject) {
                $injections[$property->getName()] = $inject;
            }
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangeSet($fqcn)
    {
        return new ChangeSet($this, $fqcn);
    }

    /**
     * @param Reader $reader
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
