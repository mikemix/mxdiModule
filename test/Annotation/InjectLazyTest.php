<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectLazy;
use mxdiModule\Factory\ProxyFactory;
use mxdiModuleTest\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectLazyTest extends TestCase
{
    /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $sm;

    /** @var InjectLazy */
    private $inject;

    public function setUp()
    {
        $this->sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->inject = new InjectLazy();
    }

    public function testGetFactoryReturnsFactoryFromServiceManager()
    {
        $factory = new LazyLoadingValueHolderFactory();

        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo(ProxyFactory::class))
            ->will($this->returnValue($factory));

        $this->assertSame($factory, $this->inject->getFactory($this->sm));
    }
}
