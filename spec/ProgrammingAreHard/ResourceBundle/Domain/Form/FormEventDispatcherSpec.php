<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Form\Event\FormEvents;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormEventDispatcherSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcher');
    }

    function it_delegates_initialize_event_dispatching_to_passed_in_dispatcher(
        EventDispatcherInterface $dispatcher,
        FormInterface $form,
        Request $request
    ) {
        $this->dispatcherIsCalledWithEvent(FormEvents::INITIALIZE, $form, $dispatcher);

        $this->initialize($form, $request);
    }

    function it_delegates_invalid_event_dispatching_to_passed_in_dispatcher(
        EventDispatcherInterface $dispatcher,
        FormInterface $form,
        Request $request
    ) {
        $this->dispatcherIsCalledWithEvent(FormEvents::INVALID, $form, $dispatcher);

        $this->invalid($form, $request);
    }

    function it_delegates_pre_handle_event_dispatching_to_passed_in_dispatcher(
        EventDispatcherInterface $dispatcher,
        FormInterface $form,
        Request $request
    ) {
        $this->dispatcherIsCalledWithEvent(FormEvents::PRE_HANDLE, $form, $dispatcher);

        $this->preHandle($form, $request);
    }

    function it_delegates_post_handle_event_dispatching_to_passed_in_dispatcher(
        EventDispatcherInterface $dispatcher,
        FormInterface $form,
        Request $request
    ) {
        $this->dispatcherIsCalledWithEvent(FormEvents::POST_HANDLE, $form, $dispatcher);

        $this->postHandle($form, $request);
    }

    function it_delegates_complete_event_dispatching_to_passed_in_dispatcher(
        EventDispatcherInterface $dispatcher,
        FormInterface $form,
        Request $request
    ) {
        $this->dispatcherIsCalledWithEvent(FormEvents::COMPLETE, $form, $dispatcher);

        $this->complete($form, $request);
    }

    private function dispatcherIsCalledWithEvent($eventName, FormInterface $form, EventDispatcherInterface $dispatcher)
    {
        $form->getName()->willReturn($formName = 'foobar');

        $dispatcher->dispatch(
            $formName . '.' . $eventName,
            Argument::type('ProgrammingAreHard\ResourceBundle\Domain\Form\Event\FormEvent')
        )->shouldBeCalled();
    }
}
