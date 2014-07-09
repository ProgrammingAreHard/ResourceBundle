<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Transformer;

interface TransformerInterface
{
    /**
     * Transform a target.
     *
     * @param $target
     * @return mixed
     */
    public function transform($target);
} 