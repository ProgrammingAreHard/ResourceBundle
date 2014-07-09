<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessorInterface;
use Prophecy\Argument;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EventfulFormProcessorSpec extends ObjectBehavior
{
    public function let(
        FormProcessorInterface $processor,
        FormEventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($processor, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormProcessor');
    }

    function it_dispatches_valid_form_events(
        FormProcessorInterface $processor,
        FormEventDispatcherInterface $eventDispatcher,
        FormInterface $form,
        Request $request
    ) {
        $eventDispatcher->initialize($form, $request)->shouldBeCalled();
        $eventDispatcher->invalid($form, $request)->shouldNotBeCalled();
        $eventDispatcher->complete($form, $request)->shouldBeCalled();

        $processor->process($form, $request)->willReturn(null);

        $this->process($form, $request)->shouldReturn(null);
    }

    function it_dispatches_invalid_form_events(
        FormProcessorInterface $processor,
        FormEventDispatcherInterface $eventDispatcher,
        FormInterface $form,
        Request $request
    ) {
        $eventDispatcher->initialize($form, $request)->shouldBeCalled();
        $eventDispatcher->complete($form, $request)->shouldNotBeCalled();
        $eventDispatcher->invalid($form, $request)->shouldBeCalled();

        $processor->process($form, $request)->willReturn($errors = array('foo' => 'bar'));

        $this->process($form, $request)->shouldReturn($errors);
    }
}
