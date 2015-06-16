<?php
namespace mxdiModule\Annotation;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class InjectConfig implements Annotation
{
    /** @var string */
    public $value;

    /** @var mixed */
    public $default = null;

    /**
     * Get the value.
     *
     * @param ServiceLocatorInterface|null $sm
     * @return object
     */
    public function getValue(ServiceLocatorInterface $sm)
    {
        try {
            return $this->read($sm->get('config'), $this->value);
        } catch (\InvalidArgumentException $e) {
            // Return default value
        }

        if ('[]' === $this->default) {
            return [];
        }

        return $this->default;
    }

    protected function read(array $config, $value)
    {
        if (isset($config[$value])) {
            // value found
            return $config[$value];
        }

        $keys = explode('.', $value);

        foreach ($keys as $key) {
            if (isset($config[$key])) {
                array_shift($keys);
                return $this->read($config[$key], implode('.', $keys));
            }
        }

        throw new \InvalidArgumentException('Key not found');
    }
}
