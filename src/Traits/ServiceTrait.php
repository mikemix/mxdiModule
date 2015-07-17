<?php
namespace mxdiModule\Traits;

trait ServiceTrait
{
    /**
     * Get canonical name of FQCN.
     *
     * @param string $fqcn
     * @return string
     */
    private function getCanonicalName($fqcn)
    {
        return strtolower(strtr($fqcn, ['-' => '', '_' => '', ' ' => '', '\\' => '', '/' => '']));
    }

    /**
     * Get hash of FQCN.
     *
     * @param string $fqcn
     * @return string
     */
    private function getHash($fqcn)
    {
        return md5($this->getCanonicalName($fqcn));
    }
} 