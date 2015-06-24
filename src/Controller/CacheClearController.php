<?php
namespace mxdiModule\Controller;

use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

class CacheClearController extends AbstractConsoleController
{
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
        if (!$this->storage instanceof FlushableInterface) {
            $this->console->writeLine('Your cache adapter is not flushable');
            return -1;
        }

        $this->storage->flush();

        return 0;
    }
}
