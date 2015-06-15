<?php
namespace mxdiModule\Service\Exception;

class CannotCreateService extends \InvalidArgumentException
{
    public static function forClass($fqcn)
    {
        return new self(sprintf(
            'No mxdi annotations found inside %s',
            $fqcn
        ));
    }
}
