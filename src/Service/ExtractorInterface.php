<?php
namespace mxdiModule\Service;

interface ExtractorInterface
{
    /**
     * Return array of values for the constructor.
     *
     * @param string $fqcn FQCN of the class
     * @return \mxdiModule\Annotation\AnnotationInterface
     */
    public function getConstructorInjections($fqcn);

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
    public function getMethodsInjections($fqcn);

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
    public function getPropertiesInjections($fqcn);

    /**
     * Shortcut to get the changeSet DTO.
     *
     * @param string $fqcn FQCN of the class
     * @return ChangeSet
     */
    public function getChangeSet($fqcn);
}
