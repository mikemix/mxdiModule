<?php
namespace mxdiModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class InjectParams implements \IteratorAggregate
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
}
