<?php
namespace mxdiModule\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

interface InstantiatorInterface
{
    /**
     * Create object based on the changeset.
     *
     * @param ServiceLocatorInterface $sm
     * @param string                  $fqcn
     * @param ChangeSet               $changeSet
     * @return object
     */
    public function create(ServiceLocatorInterface $sm, $fqcn, ChangeSet $changeSet);
}
