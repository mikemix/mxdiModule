<?php
namespace mxdiModuleTest\Controller;

use mxdiModule\Controller\ProxyClearController;
use mxdiModuleTest\TestCase;
use Zend\Console\Adapter\AdapterInterface;

class ProxyClearControllerTest extends TestCase
{
    /** @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $console;

    public function setUp()
    {
        $this->console = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['writeLine'])
            ->getMockForAbstractClass();
    }

    public function testDoesNotAcceptEmptyProxyDir()
    {
        $controller = new ProxyClearController('');
        $controller->setConsole($this->console);

        $this->assertEquals(-1, $controller->indexAction());
    }

    public function testDoesNotAcceptFakeProxyDir()
    {
        $controller = new ProxyClearController('/fake/proxy/dir');
        $controller->setConsole($this->console);

        $this->assertEquals(-1, $controller->indexAction());
    }

    public function testProxyClearAction()
    {
        $proxyDir = sprintf('%s/test', sys_get_temp_dir());
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir);
        }

        touch($proxyDir . '/proxy1.php');
        touch($proxyDir . '/proxy2.php');
        $this->assertCount(2, glob($proxyDir . '/*.php'));

        $controller = new ProxyClearController($proxyDir);
        $controller->setConsole($this->console);
        $this->assertEquals(0, $controller->indexAction());

        clearstatcache();
        $this->assertCount(0, glob($proxyDir . '/*.php'));

        rmdir($proxyDir);
    }
}
