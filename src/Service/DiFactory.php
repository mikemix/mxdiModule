<?php
namespace mxdiModule\Service;

use mxdiModule\Factory\DiAbstractFactory;
use mxdiModule\Service\Exception\CannotCreateService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

final class DiFactory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /** @var DiAbstractFactory */
    private $diAbstractFactory;

    public function __construct(DiAbstractFactory $factory = null)
    {
        $this->diAbstractFactory = $factory ?: new DiAbstractFactory();
    }

    /**
     * Create service
     *
     * @param string $fqcn FQCN of the service
     * @return object
     */
    public function __invoke($fqcn)
    {
        return $this->get($fqcn);
    }

    /**
     * Create service
     *
     * @param string $fqcn FQCN of the service
     * @return object
     *
     * @throws CannotCreateService
     */
    public function get($fqcn)
    {
        $serviceName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fqcn));

        if ($this->diAbstractFactory->canCreateServiceWithName($this->serviceLocator, $serviceName, $fqcn)) {
            return $this->diAbstractFactory->createServiceWithName($this->serviceLocator, $serviceName, $fqcn);
        }

        throw CannotCreateService::forClass($fqcn);
    }
}
