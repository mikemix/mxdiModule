<?php
namespace mxdiModule;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
    /**
     * Initialize autoloader
     */
    public function init()
    {
        AnnotationRegistry::registerLoader([$this, 'annotationAutoloader']);
    }

    /**
     * Get config
     *
     * @return array
     */
    public function getConfig()
    {
        return (array)require __DIR__ . '/../config/module.config.php';
    }

    /**
     * Get the autoloader for annotations.
     *
     * @param string $class
     * @return bool
     */
    public function annotationAutoloader($class)
    {
        if (strpos($class, 'mxdiModule\\Annotation') !== false) {
            $file = substr($class, strrpos($class, '\\')+1);
            require_once sprintf('%s/Annotation/%s.php', __DIR__, $file);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return 'mxdiModule console';
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'mxdimodule proxy clear' => 'Remove proxies from the proxy dir',
            'mxdimodule cache clear' => 'Flush the cache',
        ];
    }
}
