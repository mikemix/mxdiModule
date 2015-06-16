<?php
namespace mxdiModule\Annotation\Exception;

class CannotGetValue extends \InvalidArgumentException
{
    public static function serviceManagerMissing()
    {
        return new self('Service manager is required in this context');
    }
}
