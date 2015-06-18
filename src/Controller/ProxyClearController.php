<?php
namespace mxdiModule\Controller;

use Zend\Mvc\Controller\AbstractConsoleController;

class ProxyClearController extends AbstractConsoleController
{
    /**
     * Clear proxy files.
     *
     * @return int
     */
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('config');
        if (! isset($config['mxdimodule']['proxy_dir']) || empty($config['mxdimodule']['proxy_dir'])) {
            $this->getConsole()->writeLine('Proxy dir is not set or empty');
            return -1;
        }

        if (! is_dir($config['mxdimodule']['proxy_dir'])) {
            $this->getConsole()->writeLine('Proxy dir does not exist');
            return -1;
        }

        foreach (glob($config['mxdimodule']['proxy_dir'] . '/*.php') as $file) {
            unlink($file);
        }

        return 0;
    }
}
