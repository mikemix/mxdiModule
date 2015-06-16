<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModuleTest\TestCase;

class ChangeSetTest extends TestCase
{
    /** @var AnnotationExtractor|\PHPUnit_Framework_MockObject_MockObject */
    protected $extractor;

    public function setUp()
    {
        $this->extractor = $this->getMock(AnnotationExtractor::class, [
            'getConstructorInjections',
            'getMethodsInjections',
            'getPropertiesInjections',
        ]);
    }

    public function testGetConstructorInjections()
    {
        $params = new InjectParams();

        $this->extractor->expects($this->once())
            ->method('getConstructorInjections')
            ->with($this->equalTo('fqcn'))
            ->will($this->returnValue($params));

        $changeSet = new ChangeSet($this->extractor, 'fqcn');

        $this->assertSame($params, $changeSet->getConstructorInjections());
        $this->assertFalse($changeSet->hasSimpleConstructor());
        $this->assertTrue($changeSet->isAnnotated());
    }

    public function testHasSimpleConstructorWithSimpleObject()
    {
        $this->extractor->expects($this->once())
            ->method('getConstructorInjections')
            ->with($this->equalTo('fqcn'))
            ->will($this->returnValue(null));

        $changeSet = new ChangeSet($this->extractor, 'fqcn');
        $this->assertTrue($changeSet->hasSimpleConstructor());
        $this->assertFalse($changeSet->isAnnotated());
    }

    public function testGetMethodsInjections()
    {
        $params = [
            'setDependency' => new InjectParams(),
        ];

        $this->extractor->expects($this->once())
            ->method('getMethodsInjections')
            ->with($this->equalTo('fqcn'))
            ->will($this->returnValue($params));

        $changeSet = new ChangeSet($this->extractor, 'fqcn');

        $this->assertSame($params, $changeSet->getMethodsInjections());
        $this->assertTrue($changeSet->isAnnotated());
    }

    public function testGetPropertiesInjections()
    {
        $params = [
            'dependency' => new Inject(),
        ];

        $this->extractor->expects($this->once())
            ->method('getPropertiesInjections')
            ->with($this->equalTo('fqcn'))
            ->will($this->returnValue($params));

        $changeSet = new ChangeSet($this->extractor, 'fqcn');

        $this->assertSame($params, $changeSet->getPropertiesInjections());
        $this->assertTrue($changeSet->isAnnotated());
    }

    public function testIsSerializableToString()
    {
        $params = [
            'dependency' => new Inject(),
        ];

        $this->extractor->expects($this->once())
            ->method('getPropertiesInjections')
            ->with($this->equalTo('fqcn'))
            ->will($this->returnValue($params));

        $changeSet = new ChangeSet($this->extractor, 'fqcn');

        $this->assertInternalType('string', (string) $changeSet);
    }
}
