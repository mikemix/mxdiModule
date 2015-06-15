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

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->value;
    }
}
