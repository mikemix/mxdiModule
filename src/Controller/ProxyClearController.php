<?php
namespace mxdiModule\Controller;

use Zend\Mvc\Controller\AbstractConsoleController;

class ProxyClearController extends AbstractConsoleController
{
    /**
     * @var string
     */
    protected $proxyDir;

    /**
     * @param string $proxyDir
     */
    public function __construct($proxyDir)
    {
        $this->proxyDir = $proxyDir;
    }

    /**
     * Clear proxy files.
     *
     * @return int
     */
    public function indexAction()
    {
        if (empty($this->proxyDir)) {
            $this->getConsole()->writeLine('Proxy dir is not set or empty');
            return -1;
        }

        if (! is_dir($this->proxyDir)) {
            $this->getConsole()->writeLine('Proxy dir does not exist');
            return -1;
        }

        foreach (glob($this->proxyDir . '/*.php') as $file) {
            unlink($file);
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getProxyDir()
    {
        return $this->proxyDir;
    }
}
