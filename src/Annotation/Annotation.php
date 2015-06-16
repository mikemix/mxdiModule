<?php
namespace mxdiModule\Annotation;

use Zend\ServiceManager\ServiceLocatorInterface;

interface Annotation
{
    /**
     * Get the value.
     *
     * @param ServiceLocatorInterface $sm
     * @return mixed
     */
    public function getValue(ServiceLocatorInterface $sm);
}
