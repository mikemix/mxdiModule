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
    protected $config = [
        'service_manager' => [
            'invokables' => [
                TestObjects\DependencyA::class => TestObjects\DependencyA::class,
                TestObjects\DependencyB::class => TestObjects\DependencyB::class,
                TestObjects\DependencyC::class => TestObjects\DependencyC::class,
                TestObjects\DependencyD::class => TestObjects\DependencyD::class,
                'dependency_e'                 => TestObjects\DependencyE::class,
                'Doctrine\ORM\EntityManager'   => TestObjects\FakeDoctrine::class,
            ],
            'abstract_factories' => [
                DiAbstractFactory::class,
            ],
        ],
        'mxdimodule' => [
            'proxy_dir'       => '/tmp',
            'proxy_namespace' => 'mxdiModuleProxy',
            'cache_adapter' => 'memory',
            'cache_options' => [],
            'avoid_service' => [
                'zendmodulemanagermodulemanager' => true,
                'zendi18ntranslatortranslatorinterface' => true,
            ],
        ],
    ];

    /**
     * @return SM\ServiceManager
     */
    protected function getServiceManager()
    {
        if (! $this->sm) {
            $this->sm = new SM\ServiceManager(new SM\Config($this->config['service_manager']));
            $this->sm->setService('config', $this->config);
        }

        return $this->sm;
    }
}
