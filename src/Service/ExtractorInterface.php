<?php
namespace mxdiModule\Service;

interface ExtractorInterface
{
    /**
     * Return injections of the constructor. Most probably the InjectParams object.
     *
     * @param string $fqcn FQCN of the class
     * @return \mxdiModule\Annotation\AnnotationInterface|null
     */
    public function getConstructorInjections($fqcn);

    /**
     * Return injections for methods.
     *
     * Example array:
     *   "methodName" => mxdiModule\Annotation\AnnotationInterface object (InjectParams for example),
     *   // more methods
     *
     * @param string $fqcn FQCN of the class
     * @return array
     */
    public function getMethodsInjections($fqcn);

    /**
     * Return injections for properties.
     *
     * Example array:
     *   "propertyName" => mxdiModule\Annotation\AnnotationInterface object (InjectXXX for example),
     *   // more properties
     *
     * @param string $fqcn FQCN of the class
     * @return array
     */
    public function getPropertiesInjections($fqcn);

    /**
     * Shortcut to get the changeSet DTO.
     *
     * @param string $fqcn FQCN of the class
     * @return ChangeSet
     */
    public function getChangeSet($fqcn);
}
