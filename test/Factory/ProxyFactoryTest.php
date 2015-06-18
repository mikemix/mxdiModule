<?php
namespace mxdiModuleTest\Factory;

use mxdiModule\Factory\ProxyFactory;
use mxdiModuleTest\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class ProxyFactoryTest extends TestCase
{
    /** @var ProxyFactory */
    private $factory;

    public function setUp()
    {
        $this->factory = new ProxyFactory();
    }

    public function testCreateService()
    {
        $this->config['mxdimodule']['proxy_dir'] = sys_get_temp_dir();

        $this->assertInstanceOf(
            LazyLoadingValueHolderFactory::class,
            $this->factory->createService($this->getServiceManager())
        );
    }
}
