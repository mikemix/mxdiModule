<?php
namespace mxdiModuleTest\Controller;

use mxdiModule\Controller\CacheClearController;
use mxdiModuleTest\TestCase;
use mxdiModuleTest\TestObjects\Cache\FlushableAdapter;
use Zend\Cache\Storage\StorageInterface;
use Zend\Console\Adapter\AdapterInterface;

class CacheClearControllerTest extends TestCase
{
    /** @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $console;

    public function setUp()
    {
        $this->console = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['writeLine'])
            ->getMockForAbstractClass();
    }

    public function testErrorOnNotFlushableCacheAdapter()
    {
        /** @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject $storage */
        $storage = $this->getMockBuilder(StorageInterface::class)->getMockForAbstractClass();

        $controller = new CacheClearController($storage);
        $controller->setConsole($this->console);

        $this->assertEquals(-1, $controller->indexAction());
    }

    public function testFlushCacheAdapter()
    {
        /** @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject $storage */
        $storage = $this->getMockBuilder(FlushableAdapter::class)->getMockForAbstractClass();

        $controller = new CacheClearController($storage);
        $controller->setConsole($this->console);

        $this->assertEquals(0, $controller->indexAction());
    }
}
