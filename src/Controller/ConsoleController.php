<?php
namespace mxdiModule\Controller;

use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Request;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * Clear proxy files.
     *
     * @return int
     * @throws \BadMethodCallException
     */
    public function proxyClearAction()
    {
        if (!$this->request instanceof Request) {
            throw new \BadMethodCallException('Action allowed only from the console');
        }

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
