<?php
namespace mxdiModuleTest\TestObjects;

use mxdiModule\Service\DiFactory;
use mxdiModule\Annotation as DI;
use mxdiModule\Service\Instantiator;

class IntegrationTest
{
    /** @var DiFactory */
    public $constructorInjection;

    /** @var DiFactory */
    protected $serviceMethodInjection;

    /**
     * @var Instantiator
     * @DI\Inject("mxdiModule\Service\Instantiator", invokable=true)
     */
    private $servicePropertyInjection;

    /**
     * @var string
     * @DI\InjectConfig("mxdimodule.cache_adapter")
     */
    private $configInjectionScalar;

    /**
     * @var array
     * @DI\InjectConfig("mxdimodule.avoid_service")
     */
    private $configInjectionArray;

    /**
     * @var array
     * @DI\InjectConfig("fake.name")
     */
    private $configDefaultValue = [];

    /**
     * @var FakeDoctrine
     * @DI\InjectDoctrine
     */
    public $doctrine;

    /**
     * @param DiFactory $factory
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModule\Service\DiFactory")
     * })
     */
    public function __construct(DiFactory $factory)
    {
        $this->constructorInjection = $factory;
    }

    /**
     * @return DiFactory
     */
    public function getConstructorInjection()
    {
        return $this->constructorInjection;
    }

    /**
     * @return DiFactory
     */
    public function getServiceMethodInjection()
    {
        return $this->serviceMethodInjection;
    }

    /**
     * @param DiFactory $serviceMethodInjection
     * @DI\InjectParams({
     *     @DI\Inject("mxdiModule\Service\DiFactory")
     * })
     */
    public function setServiceMethodInjection(DiFactory $serviceMethodInjection)
    {
        $this->serviceMethodInjection = $serviceMethodInjection;
    }

    /**
     * @return Instantiator
     */
    public function getServicePropertyInjection()
    {
        return $this->servicePropertyInjection;
    }

    /**
     * @return string
     */
    public function getConfigInjectionScalar()
    {
        return $this->configInjectionScalar;
    }

    /**
     * @return array
     */
    public function getConfigInjectionArray()
    {
        return $this->configInjectionArray;
    }

    /**
     * @return array
     */
    public function getConfigDefaultValue()
    {
        return $this->configDefaultValue;
    }

    /**
     * @return FakeDoctrine
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }
}
