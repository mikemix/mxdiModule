<?php
namespace mxdiModule\Service;

use Symfony\Component\Yaml\Yaml;

class YamlExtractor implements ExtractorInterface
{
    /**
     * The configuration from the YAML file.
     * @var array
     */
    protected $config;

    /**
     * YAML parser
     * @var callable
     */
    public static $parser = [Yaml::class, 'parse'];

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['file'])) {
            throw new \InvalidArgumentException('YAML file path is missing');
        }

        $parser = self::$parser;
        $this->config = $parser($options['file']);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructorInjections($fqcn)
    {
        if (!isset($this->config[$fqcn]['constructor'])) {
            return null;
        }

        return $this->config[$fqcn]['constructor'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodsInjections($fqcn)
    {
        if (!isset($this->config[$fqcn]['methods'])) {
            return null;
        }

        return $this->config[$fqcn]['methods'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesInjections($fqcn)
    {
        if (!isset($this->config[$fqcn]['properties'])) {
            return null;
        }

        return $this->config[$fqcn]['properties'];
    }

    /**
     * {@inheritdoc}
     */
    public function getChangeSet($fqcn)
    {
        return new ChangeSet($this, $fqcn);
    }
}
