<?php
namespace mxdiModule\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class InjectParams
{
    /** @var array */
    public $value;

    /**
     * @return Inject[]
     */
    public function getInjections()
    {
        return $this->value;
    }
}
