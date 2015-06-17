<?php
namespace mxdiModuleTest;

use mxdiModule\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testInitCallable()
    {
        (new Module())->init();
    }

    public function testGetConfig()
    {
        $this->assertInternalType('array', (new Module())->getConfig());
    }

    public function testAutoloader()
    {
        $module = new Module();

        $this->assertFalse($module->annotationAutoloader('mxdiModule\Service\ChangeSet'));
        $this->assertTrue($module->annotationAutoloader('mxdiModule\Annotation\Inject'));
    }
}
