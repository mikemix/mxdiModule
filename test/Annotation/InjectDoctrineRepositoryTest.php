<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectDoctrineRepository;
use mxdiModule\Exception\CannotGetValue;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectDoctrineRepositoryTest extends TestCase
{
    /** @var object|\PHPUnit_Framework_MockObject_MockObject */
    private $em;

    /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $sm;

    public function setUp()
    {
        parent::setUp();

        $this->em = $this->getMock(\stdClass::class, ['getRepository']);

        $this->sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Doctrine\ORM\EntityManager'))
            ->will($this->returnValue($this->em));
    }

    public function testGetValueThrowsExceptionOnException()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('FQCN'))
            ->will($this->throwException(new \Exception()));

        $inject = new InjectDoctrineRepository();
        $inject->value = 'FQCN';

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue($this->sm);
    }

    public function testGetRepository()
    {
        $repository = new \stdClass();

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('FQCN'))
            ->will($this->returnValue($repository));

        $inject = new InjectDoctrineRepository();
        $inject->value = 'FQCN';

        $this->assertSame($repository, $inject->getValue($this->sm));
    }
}
