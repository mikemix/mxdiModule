<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;

class ChangeSet
{
    /** @var string */
    protected $fqcn;

    /** @var InjectParams|null */
    protected $constructorInjections;

    /** @var bool */
    protected $hasSimpleConstructor;

    /** @var InjectParams[] */
    protected $methodsInjections;

    /** @var Inject[] */
    protected $propertiesInjections;

    /** @var bool[] */
    protected $propertyVisibility;

    /** @var bool[] */
    protected $methodVisibility;

    /** @var bool */
    protected $isAnnotated = false;

    public function __construct(ExtractorInterface $extractor, $fqcn)
    {
        if (!class_exists($fqcn)) {
            return;
        }

        $this->fqcn = $fqcn;
        $this->setConstructorInjections($extractor, $fqcn);
        $this->setMethodsInjections($extractor, $fqcn);
        $this->setPropertiesInjections($extractor, $fqcn);

        $this->isAnnotated =
            $this->constructorInjections ||
            count($this->methodsInjections) ||
            count($this->propertiesInjections);
    }

    /**
     * @return string
     */
    public function getFqcn()
    {
        return $this->fqcn;
    }

    /**
     * @return InjectParams|null
     */
    public function getConstructorInjections()
    {
        return $this->constructorInjections;
    }

    /**
     * @return boolean
     */
    public function hasSimpleConstructor()
    {
        return $this->hasSimpleConstructor;
    }

    /**
     * @return InjectParams|null
     */
    public function getMethodsInjections()
    {
        return $this->methodsInjections;
    }

    /**
     * @return array|\mxdiModule\Annotation\Inject[]
     */
    public function getPropertiesInjections()
    {
        return $this->propertiesInjections;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isPropertyPublic($name)
    {
        return $this->propertyVisibility[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isMethodPublic($name)
    {
        return $this->methodVisibility[$name];
    }

    /**
     * @return boolean
     */
    public function isAnnotated()
    {
        return $this->isAnnotated;
    }

    /**
     * @param ExtractorInterface $extractor
     * @param string $fqcn
     */
    protected function setConstructorInjections(ExtractorInterface $extractor, $fqcn)
    {
        $this->constructorInjections = $extractor->getConstructorInjections($fqcn);
        $this->hasSimpleConstructor = null === $this->constructorInjections;
    }

    /**
     * @param ExtractorInterface $extractor
     * @param string $fqcn
     */
    protected function setMethodsInjections(ExtractorInterface $extractor, $fqcn)
    {
        $this->methodsInjections = (array)$extractor->getMethodsInjections($fqcn);

        foreach ($this->methodsInjections as $methodName => $injection) {
            $reflection = new \ReflectionMethod($fqcn, $methodName);
            $this->methodVisibility[$methodName] = $reflection->isPublic();
        }
    }

    /**
     * @param ExtractorInterface $extractor
     * @param string $fqcn
     */
    protected function setPropertiesInjections(ExtractorInterface $extractor, $fqcn)
    {
        $this->propertiesInjections = (array)$extractor->getPropertiesInjections($fqcn);

        foreach ($this->propertiesInjections as $propertyName => $injection) {
            $reflection = new \ReflectionProperty($fqcn, $propertyName);
            $this->propertyVisibility[$propertyName] = $reflection->isPublic();
        }
    }
}
