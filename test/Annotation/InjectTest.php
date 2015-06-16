<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\Exception\CannotGetValue;
use mxdiModule\Annotation\Inject;
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

        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo($inject->value))
            ->will($this->returnValue($service));

        $this->assertSame($service, $inject->getValue($sm));
    }

    public function testGetValueBypassesServiceManagerWithInvokable()
    {
        $inject = new Inject();
        $inject->value = self::class;
        $inject->invokable = true;

        $this->assertInstanceOf(self::class, $inject->getValue());
    }

    public function testGetValueThrowsExceptionOnMissingSMWhenRequired()
    {
        $inject = new Inject();

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue();
    }
}
