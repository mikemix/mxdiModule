<?php
namespace mxdiModuleTest;

use mxdiModule\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $this->assertInternalType('array', (new Module())->getConfig());
    }
}
