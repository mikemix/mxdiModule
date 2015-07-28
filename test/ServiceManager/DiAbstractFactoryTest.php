<?php
namespace mxdiModuleTest\ServiceManager;

use mxdiModule\ServiceManager\DiAbstractFactory;
use mxdiModule\Service\AnnotationExtractor;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\Instantiator;
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

    /** @var Instantiator|\PHPUnit_Framework_MockObject_MockObject */
    protected $instantiator;

    public function setUp()
    {
        $this->cacheAdapter = $this->getMockBuilder(StorageInterface::class)
            ->setMethods(['getItem', 'setItem'])
            ->getMockForAbstractClass();

        $this->serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->extractor = $this->getMockBuilder(AnnotationExtractor::class)
            ->setMethods(['getChangeSet'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->instantiator = $this->getMockBuilder(Instantiator::class)
            ->setMethods(['create', 'setServiceLocator'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new DiAbstractFactory($this->instantiator);
        $this->factory->setExtractor($this->extractor);
    }

    public function testCanCreateServiceWithNameReturnsResultFromCache()
    {
        $result = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheAdapter->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo(md5('injectable')))
            ->will($this->returnValue($result));

        $this->cacheAdapter->expects($this->never())
            ->method('setItem');

        $this->serviceLocator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => ['avoid_service' => []]
            ]));

        $this->serviceLocator->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('mxdiModule\Cache'))
            ->will($this->returnValue($this->cacheAdapter));

        $this->assertTrue(
            $this->factory->canCreateServiceWithName($this->serviceLocator, 'injectable', Injectable::class)
        );
    }

    public function testCanCreateServiceWithNameSetsFalseInCacheIfServiceNotAnnotated()
    {
        $this->cacheAdapter->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo(md5('injectable')))
            ->will($this->returnValue(false));

        $result = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAnnotated'])
            ->getMock();

        $result->expects($this->any())
            ->method('isAnnotated')
            ->will($this->returnValue(false));

        $this->extractor->expects($this->once())
            ->method('getChangeSet')
            ->with($this->equalTo(Injectable::class))
            ->will($this->returnValue($result));

        $this->cacheAdapter->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo(md5('injectable')), $this->equalTo(false));

        $this->serviceLocator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => ['avoid_service' => []]
            ]));

        $this->serviceLocator->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('mxdiModule\Cache'))
            ->will($this->returnValue($this->cacheAdapter));

        $this->assertFalse(
            $this->factory->canCreateServiceWithName($this->serviceLocator, 'injectable', Injectable::class)
        );
    }

    public function testCanCreateServiceWithNameSetsResultInCacheIfAnnotated()
    {
        $result = $this->getMockBuilder(ChangeSet::class)
            ->disableOriginalConstructor()
            ->setMethods(['isAnnotated'])
            ->getMock();

        $result->expects($this->any())
            ->method('isAnnotated')
            ->will($this->returnValue(true));

        $this->extractor->expects($this->once())
            ->method('getChangeSet')
            ->with($this->equalTo(Injectable::class))
            ->will($this->returnValue($result));

        $this->cacheAdapter->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo(md5('injectable')))
            ->will($this->returnValue(false));

        $this->cacheAdapter->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo(md5('injectable')), $this->equalTo($result));

        $this->serviceLocator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => ['avoid_service' => []]
            ]));

        $this->serviceLocator->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('mxdiModule\Cache'))
            ->will($this->returnValue($this->cacheAdapter));

        $this->assertTrue(
            $this->factory->canCreateServiceWithName($this->serviceLocator, 'injectable', Injectable::class)
        );
    }

    public function testCreateService()
    {
        /** @var ChangeSet|\PHPUnit_Framework_MockObject_MockObject $changeSet */
        $changeSet = $this->getMockBuilder(ChangeSet::class)
            ->setMethods(['getFqcn'])
            ->disableOriginalConstructor()
            ->getMock();

        $changeSet->expects($this->once())
            ->method('getFqcn')
            ->will($this->returnValue('fqcn'));

        $this->instantiator->expects($this->once())
            ->method('create')
            ->with($this->equalTo($this->serviceLocator), $this->equalTo('fqcn'), $this->equalTo($changeSet));

        $this->factory->setChangeSet($changeSet);
        $this->factory->createServiceWithName($this->serviceLocator, 'name', 'fqcn');
    }

    public function testFactoryAvoidsKnownServices()
    {
        $this->serviceLocator->expects($this->at(0))
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => [
                    'avoid_service' => [
                        'servicename' => true,
                    ],
                ],
            ]));

        $this->assertFalse(
            $this->factory->canCreateServiceWithName($this->serviceLocator, 'servicename', 'fqcn')
        );
    }
}
