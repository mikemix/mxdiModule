<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\DependencyA;
use mxdiModuleTest\TestObjects\DependencyB;
use mxdiModuleTest\TestObjects\DependencyC;
use mxdiModuleTest\TestObjects\DependencyD;
use mxdiModuleTest\TestObjects\Injectable;
use mxdiModuleTest\TestObjects\NoConstructor;

class AnnotationExtractorTest extends TestCase
{
    /** @var AnnotationExtractor */
    protected $service;

    public function setUp()
    {
        $this->service = new AnnotationExtractor();
    }

    public function testGetConstructorParams()
    {
        $injectA = new Inject();
        $injectA->value = DependencyA::class;

        $injectB = new Inject();
        $injectB->value = DependencyB::class;

        $params = new InjectParams();
        $params->value = [$injectA, $injectB];

        $this->assertEquals($params, $this->service->getConstructorInjections(Injectable::class));
    }

    public function testGetMethodsAnnotations()
    {
        $injectC = new Inject();
        $injectC->value = DependencyC::class;

        $injectD = new Inject();
        $injectD->value = DependencyD::class;
        $injectD->invokable = true;

        $params = new InjectParams();
        $params->value = [$injectC, $injectD];

        $this->assertEquals(['setDependency' => $params], $this->service->getMethodsInjections(Injectable::class));
    }

    public function testGetPropertiesAnnotations()
    {
        $inject = new Inject();
        $inject->value = 'dependency_e';

        $this->assertEquals(['dependencyE' => $inject], $this->service->getPropertiesInjections(Injectable::class));
    }

    public function testGetConstructorInjectionsWhenNoConstructorExistsShouldReturnNull()
    {
        $this->assertNull($this->service->getConstructorInjections(NoConstructor::class));
    }
}
