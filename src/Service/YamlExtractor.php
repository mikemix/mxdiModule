<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\InjectParams;
use Symfony\Component\Yaml\Yaml;

class YamlExtractor implements ExtractorInterface
{
    /**
     * Path to the YAML file
     * @var string
     */
    protected $file;

    /**
     * Parsed configuration
     * @var array
     */
    protected $config;

    /**
     * YAML parser
     * @var callable
     */
    public $parser = [Yaml::class, 'parse'];

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

        $this->file = $options['file'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructorInjections($fqcn)
    {
        $config = $this->getConfig($fqcn);

        if (!isset($config['constructor'])) {
            return null;
        }

        $injections = new InjectParams();
        foreach ($config['constructor'] as $spec) {
            $injections->value[] = $this->createInjectionObject($spec);
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodsInjections($fqcn)
    {
        $config = $this->getConfig($fqcn);

        if (!isset($config['methods'])) {
            return [];
        }

        $injections = [];
        foreach ($config['methods'] as $methodName => $spec) {
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
        $config = $this->getConfig($fqcn);

        if (!isset($config['properties'])) {
            return [];
        }

        $injections = [];
        foreach ($config['properties'] as $propertyName => $injection) {
            $injections[$propertyName] = $this->createInjectionObject($injection);
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangeSet($fqcn)
    {
        $config = $this->getConfig($fqcn);
        if (!empty($config['fqcn'])) {
            $fqcn = $config['fqcn'];
        }

        return new ChangeSet($this, $fqcn);
    }

    /**
     * @param string $fqcn
     * @return array
     */
    public function getConfig($fqcn)
    {
        if (!$this->config) {
            $config = call_user_func($this->parser, $this->file);
            $this->config = $config[$fqcn];
        }

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

    /**
     * @param callable $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }
}
