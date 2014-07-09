<?php

namespace ProgrammingAreHard\ResourceBundle\Domain;

interface ResourceInterface
{
    /**
     * Is the resource new?
     *
     * @return bool
     */
    public function isNew();
} 