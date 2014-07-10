<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Repository;

use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

interface ResourceRepositoryInterface
{
    /**
     * Get a new resource instance.
     *
     * @return ResourceInterface
     */
    public function newInstance();

    /**
     * Find a resource.
     *
     * @param int $id
     * @return ResourceInterface
     */
    public function find($id);
} 