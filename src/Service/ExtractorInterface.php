<?php
namespace mxdiModule\Service;

interface ExtractorInterface
{
    /**
     * Return array of values for the constructor.
     *
     * @param string $fqcn FQCN of the class
     * @return \mixed[]
     */
    public function getConstructorInjections($fqcn);

    /**
     * Return injections for methods.
     *
     * Example array:
     *   "methodName" => [
     *     "public" => true,
     *     "inject" => $injectedValue
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
     *     "inject" => $injectedValue
     *   ],
     *   // more properties
     *
     * @param string $fqcn FQCN of the class
     * @return array
     */
    public function getPropertiesInjections($fqcn);
}
