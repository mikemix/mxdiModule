<?php
namespace mxdiModuleTest\Controller;

use mxdiModule\Controller\CacheClearController;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\Cache\FlushableAdapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\PluginManager;

class CacheClearControllerTest extends TestCase
{
    /** @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $console;

    /** @var Params|\PHPUnit_Framework_MockObject_MockObject */
    private $plugin;

    /** @var PluginManager|\PHPUnit_Framework_MockObject_MockObject */
    private $pm;

    public function setUp()
    {
        $this->console = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['writeLine'])
            ->getMockForAbstractClass();

        $this->plugin = $this->getMockBuilder(Params::class)
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();

        $this->pm = $this->getMockBuilder(PluginManager::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->pm->expects($this->atLeastOnce())
            ->method('get')
            ->with($this->equalTo('params'))
            ->will($this->returnValue($this->plugin));
    }

    public function testFlushOne()
    {
        $fqcn = 'App\Service\TestService';

        /** @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject $storage */
        $storage = $this->getMockBuilder(StorageInterface::class)
            ->setMethods(['removeItem'])
            ->getMockForAbstractClass();

        $storage->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo(md5('appservicetestservice')));

        $this->plugin->expects($this->atLeastOnce())
            ->method('__invoke')
            ->with($this->equalTo('fqcn'))
            ->will($this->returnValue($fqcn));

        $controller = new CacheClearController($storage);
        $controller->setPluginManager($this->pm);

        $this->assertEquals(0, $controller->indexAction());
    }

    public function testErrorOnNotFlushableCacheAdapter()
    {
        /** @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject $storage */
        $storage = $this->getMockBuilder(StorageInterface::class)->getMockForAbstractClass();

        $controller = new CacheClearController($storage);
        $controller->setConsole($this->console);
        $controller->setPluginManager($this->pm);

        $this->assertEquals(-1, $controller->indexAction());
    }

    public function testFlushCacheAdapter()
    {
        /** @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject $storage */
        $storage = $this->getMockBuilder(FlushableAdapter::class)->getMockForAbstractClass();

        $controller = new CacheClearController($storage);
        $controller->setConsole($this->console);
        $controller->setPluginManager($this->pm);

        $this->assertEquals(0, $controller->indexAction());
    }
}
