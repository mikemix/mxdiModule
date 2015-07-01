<?php
namespace mxdiModuleTest\TestObjects;

use mxdiModule\Annotation as DI;

class PublicPrivate
{
    /**
     * Protected property injection.
     *
     * @DI\Inject("mxdiModuleTest\TestObjects\DependencyA")
     */
    private $propertyPrivate;

    /**
     * Protected property injection.
     *
     * @DI\Inject("mxdiModuleTest\TestObjects\DependencyB")
     */
    public $propertyPublic;

    /**
     * Protected method injection.
     *
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyA"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyB")
     * })
     */
    protected function setDependencyPrivate()
    {
    }

    /**
     * Public method injection.
     *
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyC"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyD")
     * })
     */
    public function setDependencyPublic()
    {
    }
}
