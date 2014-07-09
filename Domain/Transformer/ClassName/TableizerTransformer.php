<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Transformer\ClassName;

use Doctrine\Common\Inflector\Inflector;
use ProgrammingAreHard\ResourceBundle\Domain\Transformer\TransformerInterface;

class TableizerTransformer implements TransformerInterface
{
    /**
     * Name cache.
     *
     * @var string[]
     */
    private $cache = array();

    /**
     * {@inheritdoc}
     */
    public function transform($target)
    {
        if (array_key_exists($target, $this->cache)) {
            return $this->cache[$target];
        }

        $reflect = new \ReflectionClass($target);
        $class = $reflect->getShortName();
        $this->cache[$target] = Inflector::tableize($class);
        return $this->cache[$target];
    }
} 