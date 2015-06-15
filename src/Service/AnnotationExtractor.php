<?php
namespace mxdiModule\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;

class AnnotationExtractor
{
    /** @var AnnotationReader */
    protected $reader;

    /**
     * @param string $fqcn
     * @return \mxdiModule\Annotation\Inject[]|null
     */
    public function getConstructorInjections($fqcn)
    {
        return $this->getReader()->getMethodAnnotation(
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

            $inject = $this->getReader()->getMethodAnnotation(
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
            $inject = $this->getReader()->getPropertyAnnotation(
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
     * @return AnnotationReader
     */
    protected function getReader()
    {
        if (!$this->reader) {
            $this->reader = new AnnotationReader();
        }

        return $this->reader;
    }

    /**
     * @param AnnotationReader $reader
     */
    public function setReader(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }
}
