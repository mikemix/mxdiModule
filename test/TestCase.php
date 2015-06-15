<?php
namespace mxdiModuleTest;

use Doctrine\Common\Annotations\AnnotationRegistry;
use mxdiModule\Factory\DiAbstractFactory;
use mxdiModuleTest\TestObjects\DependencyA;
use mxdiModuleTest\TestObjects\DependencyB;
use mxdiModuleTest\TestObjects\DependencyC;
use mxdiModuleTest\TestObjects\DependencyD;
use mxdiModuleTest\TestObjects\DependencyE;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var \Zend\ServiceManager\ServiceManager */
    private $sm;

    /** @var array */
    private $config = [
        'invokables' => [
            DependencyA::class => DependencyA::class,
            DependencyB::class => DependencyB::class,
            DependencyC::class => DependencyC::class,
            DependencyD::class => DependencyD::class,
            'dependency_e'     => DependencyE::class,
        ],
        'abstract_factories' => [
            DiAbstractFactory::class,
        ],
    ];
    
    public function setUp()
    {
        $this->sm = new ServiceManager(new Config($this->config));

        $base = realpath(getcwd() . '/../../src');
        AnnotationRegistry::registerAutoloadNamespace('mxdiModule\\', $base);
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return $this->sm;
    }
}
