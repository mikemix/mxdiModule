<?php
namespace mxdiModule\Factory\Service;

use mxdiModule\Service\ExtractorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExtractorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ExtractorInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config  = $serviceLocator->get('config')['mxdimodule'];

        $fqcn    = $config['extractor'];
        $options = $config['extractor_options'];

        return new $fqcn($options);
    }
}
