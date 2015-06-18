<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\InjectConfig;
use mxdiModule\Exception\CannotGetValue;
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
                'some' => [
                    'nested' => [
                        'field' => [
                            'key' => 1,
                            'bool' => true,
                        ],
                    ],
                ],
            ]));
    }

    public function testGetValueThrowsExceptionOnMissingKey()
    {
        $inject = new InjectConfig();
        $inject->value = 'fake';

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue($this->sm);
    }

    public function testGetValueThrowsExceptionOnInvalidKey()
    {
        $inject = new InjectConfig();
        $inject->value = 'some.nested.field.key.';

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue($this->sm);
    }

    public function testGetValueThrowsExceptionOnInvalidNestedKey()
    {
        $inject = new InjectConfig();
        $inject->value = 'some.nested.field.key.1';

        $this->setExpectedException(CannotGetValue::class);
        $inject->getValue($this->sm);
    }

    public function testGetValueReturnInteger()
    {
         $inject = new InjectConfig();
         $inject->value = 'some.nested.field.key';

         $this->assertSame(1, $inject->getValue($this->sm));
    }

    public function testGetValueReturnBool()
    {
         $inject = new InjectConfig();
         $inject->value = 'some.nested.field.bool';

         $this->assertTrue($inject->getValue($this->sm));
    }

    public function testGetValueReturnScalar()
    {
        $inject = new InjectConfig();
        $inject->value = 'mxdimodule.adapters.test';

        $this->assertEquals('value', $inject->getValue($this->sm));
    }

    public function testGetValueReturnArray()
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
