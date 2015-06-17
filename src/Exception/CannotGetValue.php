<?php
namespace mxdiModule\Exception;

class CannotGetValue extends \RuntimeException
{
    public static function of($name)
    {
        return new self(sprintf('Cannot get value of %s', $name));
    }
}
