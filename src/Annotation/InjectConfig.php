<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class InjectConfig implements Annotation
{
    /** @var string */
    public $value;

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
        try {
            return $this->read($sm->get('config'), $this->value);
        } catch (\InvalidArgumentException $e) {
            // Return default value
        }

        throw CannotGetValue::of($this->value);
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
