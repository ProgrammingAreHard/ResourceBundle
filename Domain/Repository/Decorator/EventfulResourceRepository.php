<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Repository\Decorator;


use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvents;
use ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher\ResourceEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

class EventfulResourceRepository implements ResourceRepositoryInterface
{
    /**
     * @var ResourceRepositoryInterface
     */
    private $repository;

    /**
     * @var ResourceEventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param ResourceRepositoryInterface $repository
     * @param ResourceEventDispatcherInterface $dispatcher
     */
    public function __construct(ResourceRepositoryInterface $repository, ResourceEventDispatcherInterface $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function newInstance()
    {
        return $this->repository->newInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->repository->find($id);
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
        $this->repository->delete($resource);
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
        $this->repository->save($resource);
        $this->dispatcher->dispatch(ResourceEvents::POST_SAVE, $resource);
    }
} 