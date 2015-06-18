<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectLazy;
use mxdiModule\Factory\ProxyFactory;
use mxdiModuleTest\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
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

    public function testGetValueWithMissingFQCN()
    {
        $factory = $this->getMock(LazyLoadingValueHolderFactory::class, ['createProxy']);

        $factory->expects($this->once())
            ->method('createProxy')
            ->with($this->equalTo('serviceName'));

        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo(ProxyFactory::class))
            ->will($this->returnValue($factory));

        $this->inject->value = 'serviceName';
        $this->inject->getValue($this->sm);
    }

    public function testGetValueWithFQCN()
    {
        $factory = $this->getMock(LazyLoadingValueHolderFactory::class, ['createProxy']);

        $factory->expects($this->once())
            ->method('createProxy')
            ->with($this->equalTo('fqcn'));

        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo(ProxyFactory::class))
            ->will($this->returnValue($factory));

        $this->inject->value = 'serviceName';
        $this->inject->fqcn = 'fqcn';
        $this->inject->getValue($this->sm);
    }

    public function testGetValueReturnsFalseFromClosureOnServiceNotCreated()
    {
        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('serviceName'))
            ->will($this->throwException(new \Exception()));

        /** @var LazyLoadingValueHolderFactory|\PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMock(LazyLoadingValueHolderFactory::class, ['createProxy']);
        $factory->expects($this->once())
            ->method('createProxy')
            ->will($this->returnArgument(1));

        /** @var LazyLoadingInterface|\PHPUnit_Framework_MockObject_MockObject $proxy */
        $proxy = $this->getMockBuilder(LazyLoadingInterface::class)
            ->setMethods(['setProxyInitializer'])
            ->getMockForAbstractClass();

        $this->inject->value = 'serviceName';
        $this->inject->fqcn = 'fqcn';
        $this->inject->setFactory($factory);

        $object = new \stdClass();

        /** @var \Closure $initializer */
        $initializer = $this->inject->getValue($this->sm);
        $this->assertFalse($initializer($object, $proxy));
    }

    public function testGetValueReturnsTrueFromClosureAndDisablesInitializer()
    {
        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('serviceName'));

        /** @var LazyLoadingValueHolderFactory|\PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMock(LazyLoadingValueHolderFactory::class, ['createProxy']);
        $factory->expects($this->once())
            ->method('createProxy')
            ->will($this->returnArgument(1));

        /** @var LazyLoadingInterface|\PHPUnit_Framework_MockObject_MockObject $proxy */
        $proxy = $this->getMockBuilder(LazyLoadingInterface::class)
            ->setMethods(['setProxyInitializer'])
            ->getMockForAbstractClass();

        $proxy->expects($this->once())
            ->method('setProxyInitializer')
            ->with($this->equalTo(null));

        $this->inject->value = 'serviceName';
        $this->inject->fqcn = 'fqcn';
        $this->inject->setFactory($factory);

        $object = new \stdClass();

        /** @var \Closure $initializer */
        $initializer = $this->inject->getValue($this->sm);
        $this->assertTrue($initializer($object, $proxy));
    }
}
