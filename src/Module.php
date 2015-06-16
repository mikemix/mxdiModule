<?php
namespace mxdiModule;

use Doctrine\Common\Annotations\AnnotationRegistry;

class Module
{
    public function init()
    {
        AnnotationRegistry::registerLoader(function ($class) {
            if (strpos($class, 'mxdiModule\\Annotation') !== false) {
                require_once sprintf('%s/Annotation/%s.php', __DIR__, basename($class));
                return true;
            }

            return false;
        });
    }

    public function getConfig()
    {
        return (array)require __DIR__ . '/../config/module.config.php';
    }
}
