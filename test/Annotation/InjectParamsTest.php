<?php
namespace mxdiModuleTest\Annotation;

use mxdiModule\Annotation\Inject;
use mxdiModule\Annotation\InjectParams;
use mxdiModuleTest\TestCase;

class InjectParamsTest extends TestCase
{
    public function testIsIterable()
    {
        $params = new InjectParams();
        $params->value = [
            new Inject(),
            new Inject(),
        ];

        foreach ($params as $inject) {
            $this->assertInstanceOf(Inject::class, $inject);
        }
    }
}
 