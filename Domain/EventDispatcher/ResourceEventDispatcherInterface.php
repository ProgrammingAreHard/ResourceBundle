<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher;

use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvent;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

interface ResourceEventDispatcherInterface
{
    /**
     * Dispatch a resource event.
     *
     * @param string $name
     * @param ResourceInterface $resource
     * @return ResourceEvent
     */
    public function dispatch($name, ResourceInterface $resource);
} 