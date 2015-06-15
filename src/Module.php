<?php
namespace mxdiModule;

use Doctrine\Common\Annotations\AnnotationRegistry;

class Module
{
    public function init()
    {
        AnnotationRegistry::registerAutoloadNamespace('mxdiModule\\', __DIR__ . '/../src');
    }

    public function getConfig()
    {
        return (array)require __DIR__ . '/../config/module.config.php';
    }
}
