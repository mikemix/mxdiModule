<?php
namespace mxdiModuleTest\Factory\Cache;

use mxdiModule\Factory\Cache\CacheFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var CacheFactory */
    protected $factory;

    public function setUp()
    {
        $this->factory = new CacheFactory();
    }

    public function testCreateMemoryAdapter()
    {
        $config = [
            'mxdimodule' => [
                'cache_adapter' => 'memory',
                'cache_options' => [],
            ],
        ];

        /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject $sm */
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue($config));

        $this->factory->createService($sm);
    }
}
