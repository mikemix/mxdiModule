<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\AnnotationInterface;
use mxdiModule\Annotation\InjectParams;

class XmlExtractor implements ExtractorInterface
{
    /**
     * Path to the XML file.
     * @var array
     */
    protected $file;

    /**
     * Parsed results
     * @var \SimpleXMLElement|null
     */
    protected $config;

    /**
     * Was config parsed?
     * @var bool
     */
    protected $isConfigParsed;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['file']) || empty($options['file'])) {
            throw new \InvalidArgumentException('XML file path is missing');
        }

        if (! file_exists($options['file'])) {
            throw new \InvalidArgumentException('XML file is missing');
        }

        $this->file = $options['file'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructorInjections($fqcn)
    {
        $this->isConfigParsed($fqcn);

        if (empty($this->config)) {
            return null;
        }

        $values = [];
        foreach ($this->config->xpath('constructor/inject') as $spec) {
            $values[] = $this->createInjectionObject($spec);
        }

        if (!count($values)) {
            return null;
        }

        $injections = new InjectParams();
        $injections->value = $values;

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodsInjections($fqcn)
    {
        $this->isConfigParsed($fqcn);

        if (empty($this->config)) {
            return [];
        }

        $injections = [];
        foreach ($this->config->xpath('methods/method') as $spec) {
            $params = new InjectParams();
            foreach ($spec->xpath('inject') as $methodSpec) {
                $params->value[] = $this->createInjectionObject($methodSpec);
            }

            $injections[(string)$spec['name']] = $params;
        }

        return $injections;
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertiesInjections($fqcn)
    {
        $this->isConfigParsed($fqcn);

        if (empty($this->config)) {
            return [];
        }

        $injections = [];
        foreach ($this->config->xpath('properties/property') as $spec) {
            $injections[(string)$spec['name']] = $this->createInjectionObject($spec->xpath('inject')[0]);
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Parse the XML.
     * @param string $fqcn
     */
    private function isConfigParsed($fqcn)
    {
        if ($this->isConfigParsed) {
            return;
        }

        $module = simplexml_load_file($this->file);

        foreach ($module->xpath('/mxdiModule/service') as $service) {
            if ((string)$service['id'] === $fqcn) {
                $this->config = $service;
                break;
            }
        }

        $this->isConfigParsed = true;
    }

    /**
     * @param \SimpleXMLElement $spec
     * @return AnnotationInterface
     */
    private function createInjectionObject(\SimpleXMLElement $spec)
    {
        $injectionFqcn = (string)$spec['type'];
        $injection = new $injectionFqcn;

        foreach ($spec->xpath('param') as $param) {
            $this->createProperty($injection, $param);
        }

        return $injection;
    }

    /**
     * @param object $injection
     * @param \SimpleXMLElement $param
     */
    private function createProperty($injection, $param)
    {
        $property = (string)$param['name'];

        $type = (string)$param['type'];
        $type = empty($type) ? 'string' : $type;

        switch ($type) {
            case 'boolean': $value = (bool)(string)$param; break;
            case 'integer': $value = (int)(string)$param; break;
            case 'float': $value = (float)(string)$param; break;
            default: $value = (string)$param;
        }

        $injection->$property = $value;
    }
}
