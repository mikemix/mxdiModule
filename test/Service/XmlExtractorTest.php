<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectConfig;
use mxdiModule\Annotation\InjectDoctrine;
use mxdiModule\Annotation\InjectLazy;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\XmlExtractor;

class XmlExtractorTest extends \PHPUnit_Framework_TestCase
{
    /** @var XmlExtractor */
    private $service;

    /** @var string */
    private $file;

    public function setUp()
    {
        $this->file = __DIR__ . '/../.resources/Annotations.xml';
        $this->service = new XmlExtractor(['file' => $this->file]);
    }

    public function testDontAllowNoFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new XmlExtractor([]);
    }

    public function testDontAllowEmptyFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new XmlExtractor(['file' => '']);
    }

    public function testDontAllowMissingFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new XmlExtractor(['file' => '/fake/path.xml']);
    }

    public function testSetFile()
    {
        $this->assertEquals($this->file, $this->service->getFile());
    }

    public function testParseReturnsNullWhenNoConstructorInjections()
    {
        $this->assertNull($this->service->getConstructorInjections('EmptyService'));
        $this->assertSame([], $this->service->getMethodsInjections('EmptyService'));
        $this->assertSame([], $this->service->getPropertiesInjections('EmptyService'));
    }

    public function testParseReturnsEmptyOnMissingService()
    {
        $this->assertNull($this->service->getConstructorInjections('FakeService'));
        $this->assertSame([], $this->service->getMethodsInjections('FakeService'));
        $this->assertSame([], $this->service->getPropertiesInjections('FakeService'));
    }

    public function testGetConstructorInjections()
    {
        /** @var object $injection */
        $injection = $this->service->getConstructorInjections('Application\Service\MyService');

        /** @var object $injections */
        $injections = $injection->value;

        $this->assertCount(3, $injections);

        $this->assertInstanceOf(Inject::class, $injections[0]);
        $this->assertSame('Zend\EventManager\EventManager', $injections[0]->value);
        $this->assertSame(true, $injections[0]->invokable);
        $this->assertSame(0, $injections[0]->count);

        $this->assertInstanceOf(InjectDoctrine::class, $injections[1]);

        $this->assertInstanceOf(InjectLazy::class, $injections[2]);
        $this->assertSame('request', $injections[2]->value);
        $this->assertSame('Zend\Http\Request', $injections[2]->fqcn);
    }

    public function testGetMethodsInjections()
    {
        $injections = $this->service->getMethodsInjections('Application\Service\MyService');

        $this->assertCount(2, $injections);

        $method1 = $injections['setFactories'];

        $this->assertCount(1, $method1->value);
        $this->assertInstanceOf(InjectConfig::class, $method1->value[0]);
        $this->assertSame('service_manager.factories', $method1->value[0]->value);

        $method2 = $injections['setApplication'];

        $this->assertCount(2, $method2->value);
        $this->assertInstanceOf(InjectDoctrine::class, $method2->value[0]);
        $this->assertInstanceOf(Inject::class, $method2->value[1]);
        $this->assertSame('Zend\EventManager\EventManager', $method2->value[1]->value);
        $this->assertSame(true, $method2->value[1]->invokable);
        $this->assertSame(0, $method2->value[1]->count);
    }

    public function testGetPropertiesInjections()
    {
        $injections = $this->service->getPropertiesInjections('Application\Service\MyService');

        $this->assertCount(2, $injections);

        $property1 = $injections['invokables'];

        $this->assertInstanceOf(InjectConfig::class, $property1);
        $this->assertSame('service_manager.factories', $property1->value);

        $property2 = $injections['another'];

        $this->assertInstanceOf(Inject::class, $property2);
        $this->assertSame('Zend\EventManager\EventManager', $property2->value);
        $this->assertSame(true, $property2->invokable);
        $this->assertSame(0, $property2->count);
    }

    public function testGetChangeSet()
    {
        $this->assertInstanceOf(ChangeSet::class, $this->service->getChangeSet('Application\Service\MyService'));
    }
}
