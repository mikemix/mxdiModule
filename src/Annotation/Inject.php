<?php
namespace mxdiModule\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION", "METHOD"})
 */
final class Inject
{
    /** @var string */
    public $value;

    /** @var bool */
    public $invokable = false;

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isInvokable()
    {
        return $this->invokable;
    }
}
