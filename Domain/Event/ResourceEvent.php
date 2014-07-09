<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Event;

use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

class ResourceEvent extends Event
{
    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @param Resource $resource
     */
    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the resource.
     *
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }
}