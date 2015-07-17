<?php
namespace mxdiModuleTest\Traits;

use mxdiModuleTest\TestObjects\Traits\ServiceTraitImpl;

class ServiceTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceTraitImpl */
    private $service;

    public function setUp()
    {
        $this->service = new ServiceTraitImpl();
    }

    public function testGetCanonicalName()
    {
        $this->assertEquals('appservicecool', $this->service->getCanonicalNameStub('\A pp\Se r_vice\\Cool'));
        $this->assertEquals('appservicecool', $this->service->getCanonicalNameStub('appservicecool'));
    }

    public function testGetHash()
    {
        $this->assertEquals(md5('appservicecool'), $this->service->getHashStub('\A pp\Se r_vice\\Cool'));
    }
}
 