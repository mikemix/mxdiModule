<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectConfig;
use mxdiModule\Annotation\InjectParams;
use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\YamlExtractor;

class YamlExtractorTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $file;

    /** @var YamlExtractor */
    private $service;

    public function setUp()
    {
        $this->file = __DIR__ . '/../.resources/Services.yml';
        $this->service = new YamlExtractor(['file' => $this->file]);
    }

    public function testDontAllowNoFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new YamlExtractor([]);
    }

    public function testDontAllowEmptyFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new YamlExtractor(['file' => '']);
    }

    public function testDontAllowMissingFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new YamlExtractor(['file' => '/var/file.yml']);
    }

    public function testParseYamlConfig()
    {
        $config = ['fqcn' => [
            'some' => 'config',
        ]];

        $parser = $this->getMock(\stdClass::class, ['parse']);
        $parser->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($this->file))
            ->will($this->returnValue($config));

        $this->service->setParser([$parser, 'parse']);

        $this->assertEquals(['some' => 'config'], $this->service->getConfig('fqcn'));
    }

    public function testParseReturnsNullWhenNoConstructorInjections()
    {
        $this->assertNull($this->service->getConstructorInjections('emptyService'));
    }

    public function testParseReturnsConstructorInjections()
    {
        $injection = $this->service->getConstructorInjections('App\Service\MyService');

        $this->assertInstanceOf(InjectParams::class, $injection);
        $this->assertCount(2, $injection->value);
        $this->assertSame('Zend\EventManager\EventNamager', $injection->value[0]->value);
        $this->assertSame(true, $injection->value[0]->invokable);
        $this->assertEquals('application', $injection->value[1]->value);
    }

    public function testParseReturnsEmptyWhenNoMethodsInjections()
    {
        $this->assertSame([], $this->service->getMethodsInjections('emptyService'));
    }

    public function testParseReturnsMethodsInjections()
    {
        $injections = $this->service->getMethodsInjections('App\Service\MyService');

        $this->assertCount(2, $injections);

        $injection1 = $injections['setDependency'][0];
        $injection2 = $injections['setDependency'][1];
        $injection3 = $injections['setFactories'][0];

        $this->assertInstanceOf(Inject::class, $injection1);
        $this->assertEquals('request', $injection1->value);

        $this->assertInstanceOf(Inject::class, $injection2);
        $this->assertEquals('application', $injection2->value);

        $this->assertInstanceOf(InjectConfig::class, $injection3);
        $this->assertEquals('service_manager.factories.my\.config', $injection3->value);
    }

    public function testParseReturnsNullWhenNoPropertiesInjections()
    {
        $this->assertSame([], $this->service->getPropertiesInjections('request'));
    }

    public function testParseReturnsPropertiesInjections()
    {
        $injections = $this->service->getPropertiesInjections('App\Service\MyService');

        $this->assertCount(2, $injections);

        $injection1 = $injections['someProperty'];
        $injection2 = $injections['anotherProperty'];

        $this->assertInstanceOf(InjectConfig::class, $injection1);
        $this->assertEquals('service_manager.invokables', $injection1->value);

        $this->assertInstanceOf(Inject::class, $injection2);
        $this->assertEquals('request', $injection2->value);
    }

    public function testGetChangeSet()
    {
        $this->assertInstanceOf(ChangeSet::class, $this->service->getChangeSet('request'));
        $this->assertInstanceOf(ChangeSet::class, $this->service->getChangeSet('emptyService'));
    }
}
