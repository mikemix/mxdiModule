<?php
namespace mxdiModuleTest\Factory\Service;

use mxdiModule\Factory\Service\ExtractorFactory;
use mxdiModuleTest\TestObjects\Service\FakeExtractor;
use Zend\ServiceManager\ServiceLocatorInterface;

class ExtractorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $sm = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get'])
            ->getMockForAbstractClass();

        $sm->expects($this->once())
            ->method('get')
            ->with($this->equalTo('config'))
            ->will($this->returnValue([
                'mxdimodule' => [
                    'extractor'         => FakeExtractor::class,
                    'extractor_options' => ['file' => 'test.yml'],
                ],
            ]));

        $factory = new ExtractorFactory();

        /** @var FakeExtractor $extractor */
        $extractor = $factory->createService($sm);

        $this->assertInstanceOf(FakeExtractor::class, $extractor);
        $this->assertEquals(['file' => 'test.yml'], $extractor->options);
    }
}
