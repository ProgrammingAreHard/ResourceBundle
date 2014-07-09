<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher;

use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvent;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Transformer\TransformerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceEventDispatcher implements ResourceEventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TransformerInterface
     */
    private $classNameTransformer;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param TransformerInterface $classNameExtractor
     */
    public function __construct(EventDispatcherInterface $dispatcher, TransformerInterface $classNameTransformer)
    {
        $this->dispatcher = $dispatcher;
        $this->classNameTransformer = $classNameTransformer;
    }

    /**
     * Dispatch a resource event.
     *
     * @param string $name
     * @param ResourceInterface $resource
     * @return ResourceEvent
     */
    public function dispatch($name, ResourceInterface $resource)
    {
        $eventName = $this->createEventName($name, $resource);
        $event = $this->createEvent($resource);
        $this->dispatcher->dispatch($eventName, $event);

        return $event;
    }
    
    /**
     * Create the full event name.
     *
     * @param string $name
     * @param ResourceInterface $resource
     * @return string
     */
    protected function createEventName($name, ResourceInterface $resource)
    {
        $resourceName = $this->classNameTransformer->transform(get_class($resource));
        return $resourceName . '.' . $name; 
    }
    
    /**
     * Create a resource event object. 
     *
     * @param ResourceInterface $resource
     * @return ResourceEvent
     */ 
    protected function createEvent(ResourceInterface $resource)
    {
        return new ResourceEvent($resource);
    }
} 