<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Repository;

use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

interface ResourceRepositoryInterface
{
    /**
     * Get a new resource instance.
     *
     * @return Resource
     */
    public function newInstance();

    /**
     * Find a resource.
     *
     * @param int $id
     * @return Resource
     */
    public function find($id);

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