<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\Instantiator;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\DependencyA;
use mxdiModuleTest\TestObjects\DependencyB;
use mxdiModuleTest\TestObjects\DependencyC;
use mxdiModuleTest\TestObjects\DependencyD;
use mxdiModuleTest\TestObjects\DependencyE;
use mxdiModuleTest\TestObjects\Injectable;

class InstantiatorTest extends TestCase
{
    /** @var Instantiator */
    protected $service;

    public function setUp()
    {
        parent::setUp();

        $this->service = new Instantiator($this->getServiceManager());
    }

    public function testCreate()
    {
        $dependencyA = new Inject();
        $dependencyA->value = DependencyA::class;

        $dependencyB = new Inject();
        $dependencyB->value = DependencyB::class;

        $dependencyC = new Inject();
        $dependencyC->value = DependencyC::class;

        $dependencyD = new Inject();
        $dependencyD->value = DependencyD::class;

        $dependencyE = new Inject();
        $dependencyE->value = 'dependency_e';

        $constructorInjection = new InjectParams();
        $constructorInjection->value = [
            $dependencyA,
            $dependencyB
        ];

        $methodsInjections = new InjectParams();
        $methodsInjections->value = [
            $dependencyC,
            $dependencyD
        ];

        /** @var Injectable $object */
        $object = $this->service->create(
            Injectable::class,
            $constructorInjection,
            ['setDependency' => $methodsInjections],
            ['dependencyE' => $dependencyE]
        );

        $this->assertInstanceOf(Injectable::class, $object);
        $this->assertInstanceOf(DependencyA::class, $object->getDependencyA());
        $this->assertInstanceOf(DependencyB::class, $object->getDependencyB());
        $this->assertInstanceOf(DependencyC::class, $object->getDependencyC());
        $this->assertInstanceOf(DependencyD::class, $object->getDependencyD());
        $this->assertInstanceOf(DependencyE::class, $object->getDependencyE());
    }
}
