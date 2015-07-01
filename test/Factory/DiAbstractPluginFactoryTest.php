<?php
namespace mxdiModuleTest\Factory;

use mxdiModule\Factory\DiAbstractFactory;
use mxdiModule\Factory\DiAbstractPluginFactory;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceManager;

class DiAbstractPluginFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateServiceWithNameWithAbstractPluginManager()
    {
        $sm = new ServiceManager();

        /** @var AbstractPluginManager|\PHPUnit_Framework_MockObject_MockObject $pluginManager */
        $pluginManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->setMethods(['getServiceLocator'])
            ->getMockForAbstractClass();

        $pluginManager->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($sm));

        /** @var DiAbstractFactory|\PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMockBuilder(DiAbstractFactory::class)
            ->setMethods(['canCreateServiceWithName'])
            ->getMockForAbstractClass();

        $factory->expects($this->once())
            ->method('canCreateServiceWithName')
            ->with($this->equalTo($sm), $this->equalTo('name'), $this->equalTo('requestedName'))
            ->will($this->returnValue(true));

        $factory = new DiAbstractPluginFactory($factory);

        $this->assertTrue($factory->canCreateServiceWithName($pluginManager, 'name', 'requestedName'));
    }

    public function testCreateServiceWithNameWithAbstractPluginManager()
    {
        $sm = new ServiceManager();
        $returnValue = new \stdClass();

        /** @var AbstractPluginManager|\PHPUnit_Framework_MockObject_MockObject $pluginManager */
        $pluginManager = $this->getMockBuilder(AbstractPluginManager::class)
            ->setMethods(['getServiceLocator'])
            ->getMockForAbstractClass();

        $pluginManager->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($sm));

        /** @var DiAbstractFactory|\PHPUnit_Framework_MockObject_MockObject $factory */
        $factory = $this->getMockBuilder(DiAbstractFactory::class)
            ->setMethods(['createServiceWithName'])
            ->getMockForAbstractClass();

        $factory->expects($this->once())
            ->method('createServiceWithName')
            ->with($this->equalTo($sm), $this->equalTo('name'), $this->equalTo('requestedName'))
            ->will($this->returnValue($returnValue));

        $factory = new DiAbstractPluginFactory($factory);

        $this->assertSame($returnValue, $factory->createServiceWithName($pluginManager, 'name', 'requestedName'));
    }
}
