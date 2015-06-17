<?php
namespace mxdiModule;

use Doctrine\Common\Annotations\AnnotationRegistry;

class Module
{
    public function init()
    {
        AnnotationRegistry::registerLoader([$this, 'annotationAutoloader']);
    }

    public function getConfig()
    {
        return (array)require __DIR__ . '/../config/module.config.php';
    }

    public function annotationAutoloader($class)
    {
        if (strpos($class, 'mxdiModule\\Annotation') !== false) {
            $file = substr($class, strrpos($class, '\\')+1);
            require_once sprintf('%s/Annotation/%s.php', __DIR__, $file);
            return true;
        }

        return false;
    }
}
