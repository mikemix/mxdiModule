<?php
namespace mxdiModule\Annotation;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class InjectParams implements \IteratorAggregate, Annotation
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
     * @throws Exception\CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm = null)
    {
        $value = [];

        /** @var Inject $injection */
        foreach ($this as $injection) {
            $value[] = $injection->getValue($sm);
        }

        return $value;
    }
}
