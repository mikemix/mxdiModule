<?php
namespace mxdiModuleTest\Service;

use mxdiModule\Service\ChangeSet;
use mxdiModule\Service\YamlExtractor;

class YamlExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testDontAllowNoFileInOptions()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new YamlExtractor([]);
    }

    public function testParseYamlConfig()
    {
        $file = '/var/some/file.yml';
        $config = ['some' => 'output'];

        $parser = $this->getMock(\stdClass::class, ['parse']);
        $parser->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($file))
            ->will($this->returnValue($config));

        YamlExtractor::$parser = [$parser, 'parse'];

        $service = new YamlExtractor(['file' => $file]);

        $this->assertEquals($config, $service->getConfig());
    }

    public function testParseReturnsNullWhenNoConstructorInjections()
    {
        $service = new YamlExtractor(['file' => '']);
        $this->assertNull($service->getConstructorInjections('fqcn'));
    }

    public function testParseReturnsConstructorInjections()
    {
        $config['fqcn']['constructor'] = [
            ['name' => \stdClass::class, 'value' => 'testValue'],
            ['name' => \stdClass::class, 'value' => 'anotherValue'],
        ];

        $parser = $this->getMock(\stdClass::class, ['parse']);
        YamlExtractor::$parser = [$parser, 'parse'];

        $parser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue($config));

        $service = new YamlExtractor(['file' => '']);

        $injections = $service->getConstructorInjections('fqcn');
        $injection1 = $injections[0];
        $injection2 = $injections[1];

        $this->assertInstanceOf(\stdClass::class, $injection1);
        $this->assertEquals('testValue', $injection1->value);

        $this->assertInstanceOf(\stdClass::class, $injection2);
        $this->assertEquals('anotherValue', $injection2->value);
    }

    public function testParseReturnsNullWhenNoMethodsInjections()
    {
        $service = new YamlExtractor(['file' => '']);
        $this->assertNull($service->getMethodsInjections('fqcn'));
    }

    public function testParseReturnsMethodsInjections()
    {
        $config['fqcn']['methods'] = [
            'setDependency' => [
                ['name' => \stdClass::class, 'value' => 'testValue'],
                ['name' => \stdClass::class, 'value' => 'anotherValue'],
            ],
            'setOther' => [
                ['name' => \stdClass::class, 'value' => 'testValue'],
                ['name' => \stdClass::class, 'value' => 'anotherValue'],
            ],
        ];

        $parser = $this->getMock(\stdClass::class, ['parse']);
        YamlExtractor::$parser = [$parser, 'parse'];

        $parser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue($config));

        $service = new YamlExtractor(['file' => '']);

        $injections = $service->getMethodsInjections('fqcn');

        $injection1 = $injections['setDependency'][0];
        $injection2 = $injections['setDependency'][1];

        $injection3 = $injections['setOther'][0];
        $injection4 = $injections['setOther'][1];

        $this->assertInstanceOf(\stdClass::class, $injection1);
        $this->assertEquals('testValue', $injection1->value);
        $this->assertInstanceOf(\stdClass::class, $injection2);
        $this->assertEquals('anotherValue', $injection2->value);

        $this->assertInstanceOf(\stdClass::class, $injection3);
        $this->assertEquals('testValue', $injection3->value);
        $this->assertInstanceOf(\stdClass::class, $injection4);
        $this->assertEquals('anotherValue', $injection4->value);
    }

    public function testParseReturnsNullWhenNoPropertiesInjections()
    {
        $service = new YamlExtractor(['file' => '']);
        $this->assertNull($service->getPropertiesInjections('fqcn'));
    }

    public function testParseReturnsPropertiesInjections()
    {
        $config['fqcn']['properties'] = [
            'property1' => ['name' => \stdClass::class, 'value' => 'testValue'],
            'property2' => ['name' => \stdClass::class, 'value' => 'anotherValue'],
        ];

        $parser = $this->getMock(\stdClass::class, ['parse']);
        YamlExtractor::$parser = [$parser, 'parse'];

        $parser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue($config));

        $service = new YamlExtractor(['file' => '']);

        $injections = $service->getPropertiesInjections('fqcn');

        $injection1 = $injections['property1'];
        $injection2 = $injections['property2'];

        $this->assertInstanceOf(\stdClass::class, $injection1);
        $this->assertEquals('testValue', $injection1->value);

        $this->assertInstanceOf(\stdClass::class, $injection2);
        $this->assertEquals('anotherValue', $injection2->value);
    }

    public function testGetChangeSet()
    {
        $service = new YamlExtractor(['file' => '']);
        $this->assertInstanceOf(ChangeSet::class, $service->getChangeSet('fqcn'));
    }
}
