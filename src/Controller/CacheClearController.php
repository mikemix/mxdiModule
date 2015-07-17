<?php
namespace mxdiModule\Controller;

use mxdiModule\Traits\ServiceTrait;
use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class CacheClearController extends AbstractConsoleController
{
    use ServiceTrait;

    /** @var StorageInterface */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Clear the cache.
     *
     * @return int
     */
    public function indexAction()
    {
        if ($this->params('fqcn')) {
            return $this->flushOne($this->params('fqcn'));
        }

        return $this->flushAll();
    }

    /**
     * @param string $fqcn
     * @return int
     */
    private function flushOne($fqcn)
    {
        $this->storage->removeItem($this->getHash($fqcn));
        return 0;
    }

    /**
     * @return int
     */
    private function flushAll()
    {
        if (!$this->storage instanceof FlushableInterface) {
            $this->console->writeLine(sprintf('%s adapter is not flushable', get_class($this->storage)));
            return -1;
        }

        $this->storage->flush();

        return 0;
    }
}
