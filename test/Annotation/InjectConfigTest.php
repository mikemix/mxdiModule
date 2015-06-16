<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectConfig;
use mxdiModuleTest\TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectConfigTest extends TestCase
{
    /** @var ServiceLocatorInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $sm;

    public function setUp()
    {
        $this->sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $this->sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => [
                    'adapters' => [
                        'test' => 'value',
                    ],
                ],
            ]));
    }

    public function testGetDefaultValueNull()
    {
        $inject = new InjectConfig();
        $inject->value = 'fake.test.test';

        $this->assertNull($inject->getValue($this->sm));
    }

    public function testGetDefaultValue()
    {
        $inject = new InjectConfig();
        $inject->value = 'fake.test.test';
        $inject->default = 't3st';

        $this->assertEquals('t3st', $inject->getValue($this->sm));
    }

    public function testGetConfigReturnScalar()
    {
        $inject = new InjectConfig();
        $inject->value = 'mxdimodule.adapters.test';

        $this->assertEquals('value', $inject->getValue($this->sm));
    }

    public function testGetDefaultValueEmptyArray()
    {
        $inject = new InjectConfig();
        $inject->value = 'fake.test.test';
        $inject->default = '[]';

        $this->assertEquals([], $inject->getValue($this->sm));
    }

    public function testGetConfigReturnArray()
    {
        $inject = new InjectConfig();
        $inject->value = 'mxdimodule';

        $this->assertEquals([
            'adapters' => [
                'test' => 'value',
            ]
        ], $inject->getValue($this->sm));
    }
}
