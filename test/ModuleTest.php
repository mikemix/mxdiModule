<?php
namespace mxdiModuleTest;

use mxdiModule\Module;
use Zend\Console\Adapter\AdapterInterface;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /** @var Module */
    private $module;

    public function setUp()
    {
        $this->module = new Module();
    }

    public function testInitCallable()
    {
        $this->module->init();
    }

    public function testGetConfig()
    {
        $this->assertInternalType('array', $this->module->getConfig());
    }

    public function testAutoloader()
    {
        $this->assertFalse($this->module->annotationAutoloader('mxdiModule\Service\ChangeSet'));
        $this->assertTrue($this->module->annotationAutoloader('mxdiModule\Annotation\Inject'));
    }

    public function testGetBanner()
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();

        $banner = $this->module->getConsoleBanner($adapter);

        $this->assertNotEmpty($banner);
        $this->assertInternalType('string', $banner);
    }

    public function testGetUsage()
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();

        $usage = $this->module->getConsoleUsage($adapter);

        $this->assertNotEmpty($usage);
        $this->assertInternalType('array', $usage);
    }
}
