<?php
namespace mxdiModuleTest\TestObjects\Traits;

use mxdiModule\Traits\ServiceTrait;

class ServiceTraitImpl
{
    use ServiceTrait;

    /**
     * @param string $fqcn
     * @return string
     */
    public function getCanonicalNameStub($fqcn)
    {
        return $this->getCanonicalName($fqcn);
    }

    /**
     * @param string $fqcn
     * @return string
     */
    public function getHashStub($fqcn)
    {
        return $this->getHash($fqcn);
    }
}
 