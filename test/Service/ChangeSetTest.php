<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\ExtractorInterface;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\PublicPrivate;

class ChangeSetTest extends TestCase
{
    /** @var ExtractorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $extractor;

    public function setUp()
    {
        $this->extractor = $this->getMockBuilder(ExtractorInterface::class)
            ->setMethods([
                'getConstructorInjections',
                'getMethodsInjections',
                'getPropertiesInjections',
                'getChangeSet',
            ])
            ->getMockForAbstractClass();
    }

    public function testDontAllowInvalidFqcns()
    {
        $changeSet = new ChangeSet($this->extractor, 'invalid_fqcn');

        $this->assertFalse($changeSet->isAnnotated());
    }

    public function testGetConstructorInjectionsWithSimpleObject()
    {
        $this->extractor->expects($this->once())
            ->method('getConstructorInjections')
            ->with($this->equalTo(\stdClass::class))
            ->will($this->returnValue(null));

        $this->extractor->expects($this->once())
            ->method('getMethodsInjections')
            ->with($this->equalTo(\stdClass::class))
            ->will($this->returnValue([]));

        $this->extractor->expects($this->once())
            ->method('getPropertiesInjections')
            ->with($this->equalTo(\stdClass::class))
            ->will($this->returnValue([]));

        $changeSet = new ChangeSet($this->extractor, \stdClass::class);

        $this->assertTrue($changeSet->hasSimpleConstructor());
        $this->assertNull($changeSet->getConstructorInjections());
        $this->assertFalse($changeSet->isAnnotated());
    }

    public function testGetConstructorInjections()
    {
        $params = new InjectParams();

        $this->extractor->expects($this->once())
            ->method('getConstructorInjections')
            ->with($this->equalTo(\stdClass::class))
            ->will($this->returnValue($params));

        $this->extractor->expects($this->once())
            ->method('getMethodsInjections')
            ->with($this->equalTo(\stdClass::class))
            ->will($this->returnValue([]));

        $this->extractor->expects($this->once())
            ->method('getPropertiesInjections')
            ->with($this->equalTo(\stdClass::class))
            ->will($this->returnValue([]));

        $changeSet = new ChangeSet($this->extractor, \stdClass::class);

        $this->assertSame($params, $changeSet->getConstructorInjections());
        $this->assertFalse($changeSet->hasSimpleConstructor());
        $this->assertTrue($changeSet->isAnnotated());
    }

    public function testGetMethodsInjections()
    {
        $params = [
            'setDependencyPublic' => new InjectParams(),
            'setDependencyPrivate' => new InjectParams(),
        ];

        $this->extractor->expects($this->once())
            ->method('getConstructorInjections')
            ->with($this->equalTo(PublicPrivate::class))
            ->will($this->returnValue($params));

        $this->extractor->expects($this->once())
            ->method('getMethodsInjections')
            ->with($this->equalTo(PublicPrivate::class))
            ->will($this->returnValue($params));

        $this->extractor->expects($this->once())
            ->method('getPropertiesInjections')
            ->with($this->equalTo(PublicPrivate::class))
            ->will($this->returnValue([]));

        $changeSet = new ChangeSet($this->extractor, PublicPrivate::class);

        $this->assertSame($params, $changeSet->getMethodsInjections());
        $this->assertTrue($changeSet->isAnnotated());
        $this->assertTrue($changeSet->isMethodPublic('setDependencyPublic'));
        $this->assertFalse($changeSet->isMethodPublic('setDependencyPrivate'));
    }

    public function testGetPropertiesInjections()
    {
        $params = [
            'propertyPrivate' => new Inject(),
            'propertyPublic' => new Inject(),
        ];

        $this->extractor->expects($this->once())
            ->method('getConstructorInjections')
            ->with($this->equalTo(PublicPrivate::class))
            ->will($this->returnValue($params));

        $this->extractor->expects($this->once())
            ->method('getMethodsInjections')
            ->with($this->equalTo(PublicPrivate::class))
            ->will($this->returnValue([]));

        $this->extractor->expects($this->once())
            ->method('getPropertiesInjections')
            ->with($this->equalTo(PublicPrivate::class))
            ->will($this->returnValue($params));

        $changeSet = new ChangeSet($this->extractor, PublicPrivate::class);

        $this->assertSame($params, $changeSet->getPropertiesInjections());
        $this->assertTrue($changeSet->isAnnotated());
        $this->assertTrue($changeSet->isPropertyPublic('propertyPublic'));
        $this->assertFalse($changeSet->isPropertyPublic('propertyPrivate'));
    }
}
