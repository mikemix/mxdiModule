<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectParamsTest extends TestCase
{
    public function testIsIterable()
    {
        $params = new InjectParams();
        $params->value = [new Inject(), new Inject()];

        foreach ($params as $inject) {
            $this->assertInstanceOf(Inject::class, $inject);
        }
    }

    public function testGetValue()
    {
        $injectionA = $this->getMockBuilder(AnnotationInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();

        $injectionA->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('A'));

        $injectionB = $this->getMockBuilder(AnnotationInterface::class)
            ->setMethods(['getValue'])
            ->getMockForAbstractClass();

        $injectionB->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('B'));

        /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject $sm */
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMockForAbstractClass();

        $params = new InjectParams();
        $params->value = [$injectionA, $injectionB];

        $this->assertEquals(['A', 'B'], $params->getValue($sm));
    }

    public function testIsCountable()
    {
        $params = new InjectParams();

        $this->assertCount(0, $params);

        $params->value = [new Inject(), new Inject()];

        $this->assertCount(2, $params);
    }
}
