<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectLazy;
use mxdiModuleTest\TestCase;
use ProxyManager\Factory\AbstractBaseFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectLazyTest extends TestCase
{
    /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $sm;

    public function setUp()
    {
        $this->sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();
    }

    public function testGetFactory()
    {
        $inject = new InjectLazy();

        $this->assertInstanceOf(AbstractBaseFactory::class, $inject->getFactory());
    }
}
