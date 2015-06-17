<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class InjectParams implements \IteratorAggregate, \Countable, Annotation
{
    /** @var array */
    public $value = [];

    /**
     * @return array|\Traversable
     * @internal
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->value);
    }

    /**
     * Get the value.
     *
     * @param ServiceLocatorInterface $sm
     * @return mixed
     *
     * @throws CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm)
    {
        $value = [];

        /** @var Inject $injection */
        foreach ($this as $injection) {
            $value[] = $injection->getValue($sm);
        }

        return $value;
    }

    /**
     * @internal
     */
    public function count()
    {
        return count($this->value);
    }
}
