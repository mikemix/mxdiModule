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

    /** @var string */
    public $splitter = '-!splitter-';

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

        $splitter = $this->splitter;
        $keys = array_map(function ($item) use ($splitter) {
            return str_replace($splitter, '.', $item);
        }, explode('.', str_replace('\.', $splitter, $configKey)));

        foreach ($keys as $key) {
            if (isset($config[$key]) && is_array($config[$key])) {
                array_shift($keys);
                $input = implode('.', array_map(function ($item) {
                    return str_replace('.', '\.', $item);
                }, $keys));
                return $this->read($config[$key], $input);
            }
        }

        throw new \InvalidArgumentException('Key not found');
    }
}
