<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Manager;

use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

interface ResourceManagerInterface
{
    /**
     * Save a resource.
     *
     * @param Resource $resource
     */
    public function save(ResourceInterface $resource);

    /**
     * Delete a resource.
     *
     * @param Resource $resource
     */
    public function delete(ResourceInterface $resource);
} 