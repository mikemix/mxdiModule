<?php
namespace mxdiModule\Service;

use Doctrine\Common\Annotations\AnnotationReader as Reader;
use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\InjectParams;

class AnnotationExtractor implements ExtractorInterface
{
    /** @var Reader */
    protected $reader;

    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader ?: new Reader();
    }

    /**
     * Return array of values for the constructor.
     *
     * @param string $fqcn FQCN of the class
     * @return \mxdiModule\Annotation\AnnotationInterface
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
     * Return injections for methods.
     *
     * Example array:
     *   "methodName" => [
     *     "public" => true,
     *     "inject" => mxdiModule\Annotation\AnnotationInterface object,
     *   ],
     *   // more methods
     *
     * @param string $fqcn FQCN of the class
     * @return array
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
                    'public' => $reflectionMethod->isPublic(),
                    'inject' => $inject,
                ];
            }
        }

        return $injections;
    }

    /**
     * Return injections for properties.
     *
     * Example array:
     *   "propertyName" => [
     *     "public" => false,
     *     "inject" => mxdiModule\Annotation\AnnotationInterface object,
     *   ],
     *   // more properties
     *
     * @param string $fqcn FQCN of the class
     * @return array
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
     * Shortcut to get the changeSet DTO.
     *
     * @param string $fqcn FQCN of the class
     * @return ChangeSet
     */
    public function getChangeSet($fqcn)
    {
        return new ChangeSet($this, $fqcn);
    }
}
