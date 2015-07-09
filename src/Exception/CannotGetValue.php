<?php
namespace mxdiModule\Exception;

class CannotGetValue extends \RuntimeException
{
    public static function of($name, \Exception $previous = null)
    {
        return new self(sprintf('Cannot get value of %s', $name), 0, $previous);
    }
}
