<?php

namespace ProgrammingAreHard\ResourceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ProgrammingAreHardResourceExtension extends Extension
{
    const RESOURCE_EVENT_DISPATCHER_ID = 'pah_resource.resource.event_dispatcher';
    const FORM_EVENT_DISPATCHER_ID = 'pah_resource.form.event_dispatcher';
    const FORM_ERROR_EXTRACTOR_ID = 'pah_resource.form.error_extractor';
    const RESOURCE_CLASS_NAME_TRANSFORMER_ID = 'pah_resource.class_name.transformer';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias(self::RESOURCE_CLASS_NAME_TRANSFORMER_ID, $config['class_transformer']);
        $container->setAlias(self::FORM_ERROR_EXTRACTOR_ID, $config['form_error_extractor']);
    }
}
