<?php
namespace mxdiModuleTest\TestObjects\Cache;

use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\StorageInterface;

abstract class FlushableAdapter implements StorageInterface, FlushableInterface
{

}
