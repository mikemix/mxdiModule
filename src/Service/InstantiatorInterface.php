<?php
namespace mxdiModule\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

interface InstantiatorInterface
{
    /**
     * Create object based on the changeset.
     *
     * @param ServiceLocatorInterface $sm
     * @param ChangeSet               $changeSet
     * @return object
     */
    public function create(ServiceLocatorInterface $sm, ChangeSet $changeSet);
}
