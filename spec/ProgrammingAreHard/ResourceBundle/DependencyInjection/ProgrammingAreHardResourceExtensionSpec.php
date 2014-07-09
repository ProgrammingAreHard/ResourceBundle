<?php

namespace spec\ProgrammingAreHard\ResourceBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\DependencyInjection\ProgrammingAreHardResourceExtension as ResourceExtension;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ProgrammingAreHardResourceExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\DependencyInjection\ProgrammingAreHardResourceExtension');
    }

    function it_aliases_class_transformer(ContainerBuilder $container)
    {
        $transformerId = 'pah_resource.class_name.tableizer_transformer';
        $config = array(
            'programming_are_hard_resource' => array(
                'class_transformer' => $transformerId
            )
        );

        $this->load($config, $container);

        $container->setAlias(ResourceExtension::RESOURCE_CLASS_NAME_TRANSFORMER_ID, $transformerId)->shouldBeCalled();
    }

    function it_aliases_form_error_extractor(ContainerBuilder $container)
    {
        $errorExtractorId = 'pah_resource.form.flattened_error_extractor';
        $config = array(
            'programming_are_hard_resource' => array(
                'form_error_extractor' => $errorExtractorId
            )
        );

        $this->load($config, $container);

        $container->setAlias(ResourceExtension::FORM_ERROR_EXTRACTOR_ID, $errorExtractorId)->shouldBeCalled();
    }
}
