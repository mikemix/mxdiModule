<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\Inject;
use mxdiModule\Exception\CannotGetValue;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectTest extends TestCase
{
    public function testIsNotInvokableByDefault()
    {
        $this->assertFalse((new Inject())->invokable);
    }

    public function testGetValueWithServiceLocator()
    {
        $inject = new Inject();
        $inject->value = 'serviceName';
        $inject->invokable = false;

        $service = new \stdClass();

        /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject $sm */
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo($inject->value))
            ->will($this->returnValue($service));

        $this->assertSame($service, $inject->getValue($sm));
    }

    public function testGetValueThrowsExceptionOnMissingService()
    {
        /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject $sm */
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->will($this->throwException(new \Exception()));

        $inject = new Inject();
        $inject->value = 'fake';

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue($sm);
    }

    public function testGetValueBypassesServiceManagerWithInvokable()
    {
        $inject = new Inject();
        $inject->value = self::class;
        $inject->invokable = true;

        /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject $sm */
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->never())
            ->method('get');

        $this->assertInstanceOf(self::class, $inject->getValue($sm));
    }
}
