<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Transformer\ClassName;

use ProgrammingAreHard\ResourceBundle\Domain\Transformer\TransformerInterface;

class ClassMapTransformer implements TransformerInterface
{
    /**
     * Class => key map
     *
     * @var string[string]
     */
    private $map;

    /**
     * Constructor.
     *
     * @param array $map
     */
    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($target)
    {
        if (!array_key_exists($target, $this->map)) {
            throw new \RunTimeException(sprintf('"%s" not found in class map.', $target));
        }

        return $this->map[$target];
    }
} 