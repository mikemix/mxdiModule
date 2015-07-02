<?php
namespace mxdiModuleTest\Service;

use mxdiModule\ServiceManager\DiAbstractFactory;
use mxdiModule\Service\DiFactory;
use mxdiModule\Service\Exception\CannotCreateService;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiFactoryTest extends TestCase
{
    /** @var DiAbstractFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $abstractFactory;

    /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $sm;

    /** @var DiFactory */
    private $factory;

    public function setUp()
    {
        $this->abstractFactory = $this->getMock(
            DiAbstractFactory::class,
            ['canCreateServiceWithName', 'createServiceWithName']
        );

        $this->sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMockForAbstractClass();

        $this->factory = new DiFactory($this->abstractFactory);
        $this->factory->setServiceLocator($this->sm);
    }

    public function testGetThrowsExceptionWhenNotServiceNotAnnotated()
    {
        $this->abstractFactory->expects($this->once())
            ->method('canCreateServiceWithName')
            ->with($this->equalTo($this->sm), $this->equalTo('namespacefqcn'), $this->equalTo('namespace\FQCN'))
            ->will($this->returnValue(false));

        $this->setExpectedException(CannotCreateService::class);
        $this->factory->get('namespace\FQCN');
    }

    public function testReturnObjectWithAnnotatedService()
    {
        $obj = new \stdClass();

        $this->abstractFactory->expects($this->once())
            ->method('canCreateServiceWithName')
            ->will($this->returnValue(true));

        $this->abstractFactory->expects($this->once())
            ->method('createServiceWithName')
            ->will($this->returnValue($obj));

        $this->assertSame($obj, $this->factory->get('fqcn'));
    }

    public function testIsInvokable()
    {
        $this->abstractFactory->expects($this->once())
            ->method('canCreateServiceWithName')
            ->will($this->returnValue(false));

        $this->setExpectedException(CannotCreateService::class);
        $this->factory->__invoke('fqcn');
    }
}
