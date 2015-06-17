<?php
namespace mxdiModule\Annotation;

use mxdiModule\Exception\CannotGetValue;
use Zend\ServiceManager\ServiceLocatorInterface;

interface Annotation
{
    /**
     * Get the value.
     *
     * @param ServiceLocatorInterface $sm
     * @return mixed
     *
     * @throws CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm);
}
