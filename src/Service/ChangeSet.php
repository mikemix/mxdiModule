<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;

class ChangeSet
{
    /** @var InjectParams|null */
    protected $constructorInjections;

    /** @var bool */
    protected $hasSimpleConstructor;

    /** @var InjectParams|null */
    protected $methodsInjections;

    /** @var array|Inject[] */
    protected $propertiesInjections;

    /** @var bool */
    protected $isAnnotated;

    public function __construct(AnnotationExtractor $extractor, $fqcn)
    {
        $this->constructorInjections = $extractor->getConstructorInjections($fqcn);
        $this->hasSimpleConstructor = null === $this->constructorInjections;

        $this->methodsInjections = $extractor->getMethodsInjections($fqcn);
        $this->propertiesInjections = $extractor->getPropertiesInjections($fqcn);

        $this->isAnnotated =
            $this->constructorInjections ||
            $this->methodsInjections ||
            count($this->propertiesInjections);
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
     * @return boolean
     */
    public function isAnnotated()
    {
        return $this->isAnnotated;
    }
}
