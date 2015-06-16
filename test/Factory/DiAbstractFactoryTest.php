<?php
namespace mxdiModuleTest\Factory;

use mxdiModule\Factory\DiAbstractFactory;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\Injectable;
use Zend\Cache\Storage\Adapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiAbstractFactoryTest extends TestCase
{
    /** @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $cacheAdapter;

    /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $serviceLocator;

    /** @var AnnotationExtractor|\PHPUnit_Framework_MockObject_MockObject */
    protected $extractor;

    /** @var DiAbstractFactory */
    protected $factory;

    public function setUp()
    {
        $this->cacheAdapter = $this->getMockBuilder(StorageInterface::class)
            ->setMethods(['hasItem', 'getItem', 'setItem'])
            ->getMockForAbstractClass();

        $this->serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->serviceLocator->expects($this->any())
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => [
                    'cache_adapter' => $this->cacheAdapter,
                    'cache_options' => [],
                    'avoid_service' => [
                        'servicename' => true,
                    ],
                ],
            ]));

        $this->extractor = $this->getMockBuilder(AnnotationExtractor::class)
            ->setMethods(['getChangeSet'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new DiAbstractFactory($this->extractor);
    }

    public function testCanCreateServiceWithNameGetsCachedResultIfAvailable()
    {
        $result = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAnnotated'])
            ->getMock();

        $result->expects($this->once())
            ->method('isAnnotated')
            ->will($this->returnValue(true));

        $this->cacheAdapter->expects($this->once())
            ->method('hasItem')
            ->with($this->equalTo('injectable'))
            ->will($this->returnValue(true));

        $this->cacheAdapter->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo('injectable'))
            ->will($this->returnValue($result));

        $this->cacheAdapter->expects($this->never())
            ->method('setItem');

        $this->assertTrue(
            $this->factory->canCreateServiceWithName($this->serviceLocator, 'injectable', Injectable::class)
        );
    }

    public function testCanCreateServiceWithNameSetsResultInCache()
    {
        $result = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAnnotated'])
            ->getMock();

        $this->extractor->expects($this->once())
            ->method('getChangeSet')
            ->with($this->equalTo(Injectable::class))
            ->will($this->returnValue($result));

        $result->expects($this->once())
            ->method('isAnnotated')
            ->will($this->returnValue(false));

        $this->cacheAdapter->expects($this->once())
            ->method('hasItem')
            ->with($this->equalTo('injectable'))
            ->will($this->returnValue(false));

        $this->cacheAdapter->expects($this->never())
            ->method('getItem');

        $this->cacheAdapter->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo('injectable'), $this->equalTo($result));

        $this->assertFalse(
            $this->factory->canCreateServiceWithName($this->serviceLocator, 'injectable', Injectable::class)
        );
    }
}
