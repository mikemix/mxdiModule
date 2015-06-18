<?php
namespace mxdiModuleTest\Factory\Controller;

use mxdiModule\Controller\ProxyClearController;
use mxdiModule\Factory\Controller\ProxyClearControllerFactory;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProxyClearControllerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => [
                    'proxy_dir' => '/test/dir'
                ],
            ]));

        /** @var AbstractPluginManager|\PHPUnit_Framework_MockObject_MockObject $controllerManager */
        $controllerManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->setMethods(['getServiceLocator'])
            ->getMockForAbstractClass();

        $controllerManager->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($sm));

        $factory = new ProxyClearControllerFactory();
        $controller = $factory->createService($controllerManager);

        $this->assertInstanceOf(ProxyClearController::class, $controller);
        $this->assertEquals('/test/dir', $controller->getProxyDir());
    }
}
