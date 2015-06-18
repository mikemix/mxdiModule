<?php
namespace mxdiModule\Annotation;

use Zend\ServiceManager\ServiceLocatorInterface;
use mxdiModule\Exception\CannotGetValue;

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

    /**
     * Convert dotted notation to config value.
     *
     * @param array $config
     * @param string $configKey
     * @return mixed
     */
    protected function read(array $config, $configKey)
    {
        if (isset($config[$configKey])) {
            // value found
            return $config[$configKey];
        }

        $keys = explode('.', $configKey);

        foreach ($keys as $key) {
            if (isset($config[$key])) {
                array_shift($keys);
                return $this->read($config[$key], implode('.', $keys));
            }
        }

        throw new \InvalidArgumentException('Key not found');
    }
}
