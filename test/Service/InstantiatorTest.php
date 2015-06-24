<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\DiFactory;
use mxdiModule\Service\Instantiator;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\DependencyA;
use mxdiModuleTest\TestObjects\DependencyB;
use mxdiModuleTest\TestObjects\DependencyC;
use mxdiModuleTest\TestObjects\DependencyD;
use mxdiModuleTest\TestObjects\DependencyE;
use mxdiModuleTest\TestObjects\FakeDoctrine;
use mxdiModuleTest\TestObjects\Injectable;
use mxdiModuleTest\TestObjects\IntegrationTest;
use mxdiModuleTest\TestObjects\WithPublicProperty;

class InstantiatorTest extends TestCase
{
    /** @var Instantiator */
    protected $service;

    public function setUp()
    {
        $this->service = new Instantiator();
    }

    public function testCreateThrowsExceptionWithNoServiceLocator()
    {
        /** @var ChangeSet|\PHPUnit_Framework_MockObject_MockObject $changeSet */
        $changeSet = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectedException('InvalidArgumentException');
        $this->service->create('fqcn', $changeSet);
    }

    public function testCreateWithSimpleConstructor()
    {
        /** @var ChangeSet|\PHPUnit_Framework_MockObject_MockObject $changeSet */
        $changeSet = $this->getMockBuilder(ChangeSet::class)
            ->setMethods(['hasSimpleConstructor', 'getMethodsInjections', 'getPropertiesInjections'])
            ->disableOriginalConstructor()
            ->getMock();

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(true));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue([]));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue([]));

        $this->service->setServiceLocator($this->getServiceManager());

        $this->assertInstanceOf(\stdClass::class, $this->service->create(\stdClass::class, $changeSet));
    }

    public function testCreateWithPubliclyAvailableProperties()
    {
        $injection = $this->getMockBuilder(AnnotationInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();

        $injection->expects($this->atLeastOnce())
            ->method('getValue')
            ->will($this->returnValue('testValue'));

        /** @var ChangeSet|\PHPUnit_Framework_MockObject_MockObject $changeSet */
        $changeSet = $this->getMockBuilder(ChangeSet::class)
            ->setMethods(['hasSimpleConstructor', 'getMethodsInjections', 'getPropertiesInjections'])
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->service->setServiceLocator($this->getServiceManager());

        /** @var WithPublicProperty $object */
        $object = $this->service->create(WithPublicProperty::class, $changeSet);

        $this->assertInstanceOf(WithPublicProperty::class, $object);
        $this->assertEquals('testValue', $object->propertyNull);
        $this->assertEquals('testValue', $object->propertyString);
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

        /** @var ChangeSet|\PHPUnit_Framework_MockObject_MockObject $changeSet */
        $changeSet = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getConstructorInjections',
                'hasSimpleConstructor',
                'getMethodsInjections',
                'getPropertiesInjections',
            ])
            ->getMock();

        $changeSet->expects($this->once())
            ->method('getConstructorInjections')
            ->will($this->returnValue($constructorInjection));

        $changeSet->expects($this->once())
            ->method('hasSimpleConstructor')
            ->will($this->returnValue(false));

        $changeSet->expects($this->once())
            ->method('getMethodsInjections')
            ->will($this->returnValue(['setDependency' => $methodsInjections]));

        $changeSet->expects($this->once())
            ->method('getPropertiesInjections')
            ->will($this->returnValue(['dependencyE' => $dependencyE]));

        $this->service->setServiceLocator($this->getServiceManager());

        /** @var Injectable $object */
        $object = $this->service->create(Injectable::class, $changeSet);

        $this->assertInstanceOf(Injectable::class, $object);
        $this->assertInstanceOf(DependencyA::class, $object->getDependencyA());
        $this->assertInstanceOf(DependencyB::class, $object->getDependencyB());
        $this->assertInstanceOf(DependencyC::class, $object->getDependencyC());
        $this->assertInstanceOf(DependencyD::class, $object->getDependencyD());
        $this->assertInstanceOf(DependencyE::class, $object->getDependencyE());
    }

    /**
     * Integration test to ultimately check if module is working correctly.
     * Check all features as well.
     */
    public function testCreateIntegration()
    {
        $this->config['service_manager']['invokables'][DiFactory::class] = DiFactory::class;

        /** @var IntegrationTest $object */
        $object = $this->getServiceManager()->get(IntegrationTest::class);

        $this->assertInstanceOf(DiFactory::class, $object->getConstructorInjection());
        $this->assertInstanceOf(DiFactory::class, $object->getServiceMethodInjection());
        $this->assertInstanceOf(Instantiator::class, $object->getServicePropertyInjection());
        $this->assertInternalType('string', $object->getConfigInjectionScalar());
        $this->assertNotEmpty($object->getConfigInjectionArray());
        $this->assertInternalType('array', $object->getConfigDefaultValue());
        $this->assertInstanceOf(FakeDoctrine::class, $object->getDoctrine());
    }
}
