<?php
namespace mxdiModuleTest\TestObjects;

use mxdiModule\Annotation as DI;

class Injectable
{
    /** @var DependencyA */
    public $dependencyA;

    /** @var DependencyB */
    public $dependencyB;

    /** @var DependencyC */
    private $dependencyC;

    /** @var DependencyD */
    private $dependencyD;

    /**
     * Property injection
     *
     * @var DependencyE
     * @DI\Inject("dependency_e")
     */
    private $dependencyE;


    /**
     * Constructor injection.
     *
     * @param DependencyA $dep1
     * @param DependencyB $dep2
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyA"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyB")
     * })
     */
    public function __construct(DependencyA $dep1, DependencyB $dep2)
    {
        $this->dependencyA = $dep1;
        $this->dependencyB = $dep2;
    }

    /**
     * Method injection.
     *
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyC"),
     *     @DI\Inject("mxdiModuleTest\TestObjects\DependencyD", invokable=true)
     * })
     * @param DependencyC $dependencyC
     * @param DependencyD $dependencyD
     */
    protected function setDependency(DependencyC $dependencyC, DependencyD $dependencyD)
    {
        $this->dependencyC = $dependencyC;
        $this->dependencyD = $dependencyD;
    }

    /**
     * @return DependencyA
     */
    public function getDependencyA()
    {
        return $this->dependencyA;
    }

    /**
     * @return DependencyB
     */
    public function getDependencyB()
    {
        return $this->dependencyB;
    }

    /**
     * @return DependencyC
     */
    public function getDependencyC()
    {
        return $this->dependencyC;
    }

    /**
     * @return DependencyD
     */
    public function getDependencyD()
    {
        return $this->dependencyD;
    }

    /**
     * @return DependencyE
     */
    public function getDependencyE()
    {
        return $this->dependencyE;
    }
}
