<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\PublicPrivate;
use mxdiModuleTest\TestObjects\WithPublicProperty;
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

        $this->service->setServiceLocator(new ServiceManager());

        $this->assertInstanceOf(\stdClass::class, $this->service->create(\stdClass::class, $changeSet));
    }

    public function testCreateWithoutSimpleConstructor()
    {
        $this->service->setServiceLocator(new ServiceManager());

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

        $this->service->create(\stdClass::class, $changeSet);
    }

    public function testCreateWithNotAccessibleMethods()
    {
        $this->service->setServiceLocator(new ServiceManager());

        $params = [new \stdClass()];

        $injections = [
            'setDependencyPrivate' => [
                'public' => false,
                'inject' => $this->getInjectionMock($params),
            ],
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

        $object = $this->service->create(PublicPrivate::class, $changeSet);

        $this->assertInstanceOf(PublicPrivate::class, $object);
    }

    public function testCreateWithAccessibleMethods()
    {
        $this->service->setServiceLocator(new ServiceManager());

        $params = [new \stdClass()];

        $injections = [
            'setDependencyPublic' => [
                'public' => true,
                'inject' => $this->getInjectionMock($params),
            ],
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

        $object = $this->service->create(PublicPrivate::class, $changeSet);

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
                'propertyNull' => [
                    'public' => true,
                    'inject' => $injection,
                ],
                'propertyString' => [
                    'public' => true,
                    'inject' => $injection,
                ],
            ]));

        $this->service->setServiceLocator(new ServiceManager());

        /** @var WithPublicProperty $object */
        $object = $this->service->create(WithPublicProperty::class, $changeSet);

        $this->assertInstanceOf(WithPublicProperty::class, $object);
        $this->assertEquals('testValue', $object->propertyNull);
        $this->assertEquals('testValue', $object->propertyString);
    }

    public function testCreateWithNotAccessibleProperties()
    {
        $this->service->setServiceLocator(new ServiceManager());

        $params = new \stdClass();

        $injections = [
            'propertyPrivate' => [
                'public' => false,
                'inject' => $this->getInjectionMock($params),
            ],
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

        $this->service->create(PublicPrivate::class, $changeSet);
    }

    public function testCreateThrowsExceptionWithNoServiceLocator()
    {
        $changeSet = $this->getChangeSetMock();

        $this->setExpectedException('InvalidArgumentException');
        $this->service->create('fqcn', $changeSet);
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
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }
}
