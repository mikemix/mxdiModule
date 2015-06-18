<?php
namespace mxdiModule\Controller;

use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class ConsoleController extends AbstractConsoleController
{
    /**
     * Clear proxy files.
     *
     * @return int
     */
    public function proxyClearAction()
    {
        $config = $this->getServiceLocator()->get('config');
        if (! isset($config['mxdimodule']['proxy_dir']) || empty($config['mxdimodule']['proxy_dir'])) {
            $this->console()->writeLine('Proxy dir is not set or empty');
            return -1;
        }

        if (! is_dir($config['mxdimodule']['proxy_dir'])) {
            $this->console()->writeLine('Proxy dir does not exist');
            return -1;
        }

        foreach (glob($config['mxdimodule']['proxy_dir'] . '/*.php') as $file) {
            unlink($file);
        }

        return 0;
    }

    /**
     * @return AdapterInterface
     */
    protected function console()
    {
        return $this->getServiceLocator()->get('console');
    }
}
