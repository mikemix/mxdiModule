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
     *
     * @throws Exception\CannotGetValue
     */
    public function getValue(ServiceLocatorInterface $sm = null);
}
