<?php
namespace mxdiModuleTest\Factory\Controller;

use mxdiModule\Controller\CacheClearController;
use mxdiModule\Factory\Controller\CacheClearControllerFactory;
use mxdiModuleTest\TestCase;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheClearControllerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $adapter = $this->getMockBuilder(StorageInterface::class)->getMockForAbstractClass();

        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('mxdiModule\Cache'))
            ->will($this->returnValue($adapter));

        /** @var AbstractPluginManager|\PHPUnit_Framework_MockObject_MockObject $controllerManager */
        $controllerManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->setMethods(['getServiceLocator'])
            ->getMockForAbstractClass();

        $controllerManager->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($sm));

        $factory = new CacheClearControllerFactory();
        $controller = $factory->createService($controllerManager);

        $this->assertInstanceOf(CacheClearController::class, $controller);
    }
}
