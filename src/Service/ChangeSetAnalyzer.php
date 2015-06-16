<?php
namespace mxdiModule\Service;

use mxdiModule\Annotation\InjectParams;

class ChangeSetAnalyzer
{
    /**
     * @param InjectParams|null $constructorInjections
     * @return int
     */
    public function getConstructorParameterCount(InjectParams $constructorInjections = null)
    {
        return $constructorInjections ? count($constructorInjections) : 0;
    }

    /**
     * @param \Traversable $methodInjections
     * @return array
     */
    public function getMethodMetadata($methodInjections)
    {

    }
}
