<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectDoctrine;
use mxdiModule\Exception\CannotGetValue;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectDoctrineTest extends TestCase
{
    public function testGetValueThrowsExceptionOnMissingDoctrine()
    {
        $inject = new InjectDoctrine();

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue($this->getServiceManager());
    }

    public function testGetDoctrine()
    {
        $em = new \stdClass();

        /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject $sm */
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Doctrine\ORM\EntityManager'))
            ->will($this->returnValue($em));

        $inject = new InjectDoctrine();

        $this->assertSame($em, $inject->getValue($sm));
    }
}
