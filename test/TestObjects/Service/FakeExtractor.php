<?php
namespace mxdiModuleTest\TestObjects\Service;

use mxdiModule\Service\ExtractorInterface;

class FakeExtractor implements ExtractorInterface
{
    public $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getConstructorInjections($fqcn)
    {
    }

    public function getMethodsInjections($fqcn)
    {
    }

    public function getPropertiesInjections($fqcn)
    {
    }

    public function getChangeSet($fqcn)
    {
    }
}
