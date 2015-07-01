<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\Constructor;
use mxdiModuleTest\TestObjects\PublicPrivate;
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

    public function testGetConstructorInjections()
    {
        $injectA = new Inject();
        $injectA->value = DependencyA::class;

        $injectB = new Inject();
        $injectB->value = DependencyB::class;

        $params = new InjectParams();
        $params->value = [$injectA, $injectB];

        $this->assertEquals($params, $this->service->getConstructorInjections(Injectable::class));
    }

    public function testGetMethodsInjectionsIgnoresConstructor()
    {
        $this->assertEmpty($this->service->getMethodsInjections(Constructor::class));
    }

    public function testGetChangeSet()
    {
        $this->assertInstanceOf(ChangeSet::class, $this->service->getChangeSet('fqcn'));
    }

    public function testGetMethodsInjections()
    {
        $paramsPrivate = new InjectParams();
        $paramsPrivate->value = [
            $this->createInjectionFor(DependencyA::class),
            $this->createInjectionFor(DependencyB::class)
        ];

        $paramsPublic = new InjectParams();
        $paramsPublic->value = [
            $this->createInjectionFor(DependencyC::class),
            $this->createInjectionFor(DependencyD::class)
        ];

        $expected = [
            'setDependencyPrivate' => $paramsPrivate,
            'setDependencyPublic' => $paramsPublic,
        ];

        $this->assertEquals($expected, $this->service->getMethodsInjections(PublicPrivate::class));
    }

    public function testGetPropertiesInjections()
    {
        $expected = [
            'propertyPrivate' => $this->createInjectionFor(DependencyA::class),
            'propertyPublic' => $this->createInjectionFor(DependencyB::class),
        ];

        $this->assertEquals($expected, $this->service->getPropertiesInjections(PublicPrivate::class));
    }

    public function testGetConstructorInjectionsWhenNoConstructorExistsShouldReturnNull()
    {
        $this->assertNull($this->service->getConstructorInjections(NoConstructor::class));
    }

    private function createInjectionFor($fqcn)
    {
        $inject = new Inject();
        $inject->value = $fqcn;

        return $inject;
    }
}
