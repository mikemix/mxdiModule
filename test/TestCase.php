<?php
namespace mxdiModuleTest;

use mxdiModule\Factory\DiAbstractFactory;
use mxdiModuleTest\TestObjects;
use Zend\ServiceManager as SM;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var SM\ServiceManager */
    private $sm;

    /** @var array */
    private $config = [
        'invokables' => [
            TestObjects\DependencyA::class => TestObjects\DependencyA::class,
            TestObjects\DependencyB::class => TestObjects\DependencyB::class,
            TestObjects\DependencyC::class => TestObjects\DependencyC::class,
            TestObjects\DependencyD::class => TestObjects\DependencyD::class,
            'dependency_e'                 => TestObjects\DependencyE::class,
        ],
        'abstract_factories' => [
            DiAbstractFactory::class,
        ],
    ];

    /**
     * @return SM\ServiceManager
     */
    protected function getServiceManager()
    {
        if (! $this->sm) {
            $this->sm = new SM\ServiceManager(new SM\Config($this->config));
        }

        return $this->sm;
    }

    /**
     * @param array $config
     */
    public function setServiceManagerConfig(array $config)
    {
        $this->config = $config;
    }
}
