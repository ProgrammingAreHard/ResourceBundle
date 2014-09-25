<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form\Factory;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormErrorExtractorInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use Prophecy\Argument;

class EventfulFormProcessorFactorySpec extends ObjectBehavior
{
    public function let(
        FormEventDispatcherInterface $dispatcher,
        FormErrorExtractorInterface $errorExtractor
    ) {
        $this->beConstructedWith($dispatcher, $errorExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\Factory\EventfulFormProcessorFactory');
    }

    function it_can_create_a_form_processor(FormHandlerInterface $handler)
    {
        $this->create($handler)->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormProcessor');
    }
}
