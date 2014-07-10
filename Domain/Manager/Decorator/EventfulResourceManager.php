<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Manager\Decorator;

use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvents;
use ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher\ResourceEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Manager\ResourceManagerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

class EventfulResourceManager implements ResourceManagerInterface
{
    /**
     * @var ResourceEventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var
     * ResourceManagerInterface
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param ResourceManagerInterface $manager
     * @param ResourceEventDispatcherInterface $dispatcher
     */
    public function __construct(ResourceManagerInterface $manager, ResourceEventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ResourceInterface $resource)
    {
        if ($resource->isNew()) {
            $this->create($resource);
        } else {
            $this->update($resource);
        }
    }

    /**
     * Eventfully create a resource.
     *
     * @param ResourceInterface $resource
     */
    private function create(ResourceInterface $resource)
    {
        $this->dispatcher->dispatch(ResourceEvents::PRE_CREATE, $resource);
        $this->eventfullySave($resource);
        $this->dispatcher->dispatch(ResourceEvents::POST_CREATE, $resource);
    }

    /**
     * Eventfully update a resource.
     *
     * @param ResourceInterface $resource
     */
    private function update(ResourceInterface $resource)
    {
        $this->dispatcher->dispatch(ResourceEvents::PRE_UPDATE, $resource);
        $this->eventfullySave($resource);
        $this->dispatcher->dispatch(ResourceEvents::POST_UPDATE, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ResourceInterface $resource)
    {
        $this->dispatcher->dispatch(ResourceEvents::PRE_DELETE, $resource);
        $this->manager->delete($resource);
        $this->dispatcher->dispatch(ResourceEvents::POST_DELETE, $resource);
    }

    /**
     * Eventfully save a resource.
     *
     * @param ResourceInterface $resource
     */
    private function eventfullySave(ResourceInterface $resource)
    {
        $this->dispatcher->dispatch(ResourceEvents::PRE_SAVE, $resource);
        $this->manager->save($resource);
        $this->dispatcher->dispatch(ResourceEvents::POST_SAVE, $resource);
    }
}