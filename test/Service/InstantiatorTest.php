<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\PublicPrivate;
use mxdiModuleTest\TestObjects\PublicProperties;
use Zend\ServiceManager\ServiceManager;

class InstantiatorTest extends TestCase
{
    /** @var Instantiator */
    protected $service;

    public function setUp()
    {
        $this->service = new Instantiator();
    }

    public function testCreateWithSimpleConstructor()
    {
        $changeSet = $this->getChangeSetMock();

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(true));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue([]));

        $this->assertInstanceOf(\stdClass::class, $this->service->create(
            new ServiceManager(),
            \stdClass::class,
            $changeSet
        ));
    }

    public function testCreateWithoutSimpleConstructor()
    {
        $inject = $this->getInjectionMock([]);

        $changeSet = $this->getChangeSetMock();

        $changeSet->expects($this->once())
            ->method('getConstructorInjections')
            ->will($this->returnValue($inject));

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(false));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue([]));

        $this->service->create(new ServiceManager(), \stdClass::class, $changeSet);
    }

    public function testCreateWithNotAccessibleMethods()
    {
        $params = [new \stdClass()];

        $injections = [
            'setDependencyPrivate' => $this->getInjectionMock($params),
        ];

        $changeSet = $this->getChangeSetMock();

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(true));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue($injections));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('isMethodPublic')
            ->with($this->equalTo('setDependencyPrivate'))
            ->will($this->returnValue(false));

        $object = $this->service->create(new ServiceManager(), PublicPrivate::class, $changeSet);

        $this->assertInstanceOf(PublicPrivate::class, $object);
    }

    public function testCreateWithAccessibleMethods()
    {
        $params = [new \stdClass()];

        $injections = [
            'setDependencyPublic' => $this->getInjectionMock($params),
        ];

        $changeSet = $this->getChangeSetMock();

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(true));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue($injections));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('isMethodPublic')
            ->with($this->equalTo('setDependencyPublic'))
            ->will($this->returnValue(true));

        $object = $this->service->create(new ServiceManager(), PublicPrivate::class, $changeSet);

        $this->assertInstanceOf(PublicPrivate::class, $object);
    }

    public function testCreateWithAccessibleProperties()
    {
        $injection = $this->getInjectionMock('testValue', 'atLeastOnce');

        $changeSet = $this->getChangeSetMock();

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(true));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue([
                'propertyNull' => $injection,
                'propertyString' => $injection,
            ]));

        $changeSet->expects($this->atLeastOnce())
            ->method('isPropertyPublic')
            ->will($this->returnValue(true));

        /** @var PublicProperties $object */
        $object = $this->service->create(new ServiceManager(), PublicProperties::class, $changeSet);

        $this->assertInstanceOf(PublicProperties::class, $object);
        $this->assertEquals('testValue', $object->propertyNull);
        $this->assertEquals('testValue', $object->propertyString);
    }

    public function testCreateWithNotAccessibleProperties()
    {
        $params = new \stdClass();

        $injections = [
            'propertyPrivate' => $this->getInjectionMock($params),
        ];

        $changeSet = $this->getChangeSetMock();

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(true));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue($injections));

        $changeSet->expects($this->atLeastOnce())
            ->method('isPropertyPublic')
            ->with($this->equalTo('propertyPrivate'))
            ->will($this->returnValue(false));

        $this->service->create(new ServiceManager(), PublicPrivate::class, $changeSet);
    }

    /**
     * @param mixed $returnValue
     * @param string $howMany
     * @return AnnotationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getInjectionMock($returnValue, $howMany = 'once')
    {
        $mock = $this->getMockBuilder(AnnotationInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();

        $mock->expects($this->$howMany())
            ->method('getValue')
            ->will($this->returnValue($returnValue));

        return $mock;
    }

    /**
     * @return ChangeSet|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getChangeSetMock()
    {
        return $this->getMockBuilder(ChangeSet::class)
            ->setMethods([
                'hasSimpleConstructor',
                'getConstructorInjections',
                'getMethodsInjections',
                'getPropertiesInjections',
                'isPropertyPublic',
                'isMethodPublic',
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }
}
