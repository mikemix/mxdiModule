<?php
namespace mxdiModuleTest\Controller;

use mxdiModule\Controller\ProxyClearController;
use mxdiModuleTest\TestCase;
use Zend\Console\Adapter\AdapterInterface;

class ProxyClearControllerTest extends TestCase
{
    /** @var ProxyClearController */
    private $controller;

    /** @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $console;

    public function setUp()
    {
        $this->console = $this->getMockBuilder(AdapterInterface::class)
            ->setMethods(['writeLine'])
            ->getMockForAbstractClass();

        $this->controller = new ProxyClearController();
        $this->controller->setConsole($this->console);
    }

    public function testDoesNotAcceptMissingProxyDir()
    {
        unset($this->config['mxdimodule']['proxy_dir']);
        $this->controller->setServiceLocator($this->getServiceManager());

        $this->assertEquals(-1, $this->controller->indexAction());
    }

    public function testDoesNotAcceptEmptyProxyDir()
    {
        $this->config['mxdimodule']['proxy_dir'] = '';
        $this->controller->setServiceLocator($this->getServiceManager());

        $this->assertEquals(-1, $this->controller->indexAction());
    }

    public function testDoesNotAcceptFakeProxyDir()
    {
        $this->config['mxdimodule']['proxy_dir'] = '/fake/proxy/dir';
        $this->controller->setServiceLocator($this->getServiceManager());

        $this->assertEquals(-1, $this->controller->indexAction());
    }

    public function testProxyClearAction()
    {
        $proxyDir = sprintf('%s/test', sys_get_temp_dir());
        if (!is_dir($proxyDir)) {
            mkdir($proxyDir);
        }

        $this->config['mxdimodule']['proxy_dir'] = $proxyDir;
        $this->controller->setServiceLocator($this->getServiceManager());

        touch($proxyDir . '/proxy1.php');
        touch($proxyDir . '/proxy2.php');

        $this->assertCount(2, glob($proxyDir . '/*.php'));

        $this->assertEquals(0, $this->controller->indexAction());

        clearstatcache();
        $this->assertCount(0, glob($proxyDir . '/*.php'));

        rmdir($proxyDir);
    }
}
