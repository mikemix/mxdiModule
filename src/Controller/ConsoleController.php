<?php
namespace mxdiModule\Controller;

use Zend\Console\Request;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController
{
    /**
     * Clear proxy files.
     *
     * @return string
     * @throws \BadMethodCallException
     */
    public function proxyClearAction()
    {
        if (!$this->request instanceof Request) {
            throw new \BadMethodCallException('Action allowed only from the console');
        }

        $config = $this->getServiceLocator()->get('config');
        if (! isset($config['mxdimodule']['proxy_dir']) || empty($config['mxdimodule']['proxy_dir'])) {
            return 'Proxy dir is not set or empty';
        }

        foreach (glob($config['mxdimodule']['proxy_dir'] . '/*.php') as $file) {
            unlink($file);
        }

        return '';
    }
}
