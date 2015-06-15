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
use mxdiModuleTest\TestObjects\DependencyE;
use mxdiModuleTest\TestObjects\Injectable;

class AnnotationExtractorTest extends TestCase
{
    /** @var AnnotationExtractor */
    protected $service;

    public function setUp()
    {
        parent::setUp();

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
        $injectA = new Inject();
        $injectA->value = DependencyC::class;

        $injectB = new Inject();
        $injectB->value = DependencyD::class;

        $params = new InjectParams();
        $params->value = [$injectA, $injectB];

        $this->assertEquals(['setDependency' => $params], $this->service->getMethodsInjections(Injectable::class));
    }

    public function testGetPropertiesAnnotations()
    {
        $inject = new Inject();
        $inject->value = 'dependency_e';

        $this->assertEquals(['dependencyE' => $inject], $this->service->getPropertiesInjections(Injectable::class));
    }
}
