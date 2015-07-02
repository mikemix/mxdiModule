<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\InjectParams;
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
        if (empty($options['file'])) {
            throw new \InvalidArgumentException('YAML file path missing');
        }

        if (!file_exists($options['file'])) {
            throw new \InvalidArgumentException('YAML file missing');
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

        $injections = new InjectParams();
        foreach ($this->config[$fqcn]['constructor'] as $spec) {
            $injections->value[] = $this->createInjectionObject($spec);
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodsInjections($fqcn)
    {
        if (!isset($this->config[$fqcn]['methods'])) {
            return [];
        }

        $injections = [];
        foreach ($this->config[$fqcn]['methods'] as $methodName => $spec) {
            foreach ($spec as $injection) {
                $injections[$methodName][] = $this->createInjectionObject($injection);
            }
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesInjections($fqcn)
    {
        if (!isset($this->config[$fqcn]['properties'])) {
            return [];
        }

        $injections = [];
        foreach ($this->config[$fqcn]['properties'] as $propertyName => $injection) {
            $injections[$propertyName] = $this->createInjectionObject($injection);
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangeSet($fqcn)
    {
        return new ChangeSet($this, $fqcn);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $spec
     * @return AnnotationInterface
     */
    private function createInjectionObject(array $spec)
    {
        $injectionFqcn = $spec['type'];
        $injection = new $injectionFqcn;
        unset($spec['type']);

        foreach ($spec as $property => $value) {
            $injection->$property = $value;
        }

        return $injection;
    }
}
