<?php

namespace ProgrammingAreHard\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('programming_are_hard_resource');

        $rootNode
            ->children()
                ->scalarNode('class_transformer')->defaultValue('pah_resource.class_name.underscore_transformer')->end()
                ->scalarNode('form_error_extractor')->defaultValue('pah_resource.form.flattened_error_extractor')->end()
            ->end();

        return $treeBuilder;
    }
}
