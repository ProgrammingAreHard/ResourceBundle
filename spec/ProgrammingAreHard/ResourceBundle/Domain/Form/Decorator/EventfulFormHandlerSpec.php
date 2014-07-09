<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use Prophecy\Argument;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class EventfulFormHandlerSpec extends ObjectBehavior
{
    public function let(FormHandlerInterface $handler, FormEventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($handler, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormHandler');
    }

    function it_dispatches_events_when_handling_form(
        FormHandlerInterface $handler,
        FormEventDispatcherInterface $dispatcher,
        FormInterface $form,
        Request $request
    ) {
        $dispatcher->preHandle($form, $request)->shouldNotBeCalled();
        $dispatcher->postHandle($form, $request)->shouldNotBeCalled();

        $handler->handle($form, $request)->will(function() use ($dispatcher, $form, $request) {
                $dispatcher->preHandle($form, $request)->shouldBeCalled();
                $dispatcher->postHandle($form, $request)->shouldBeCalled();
        });

        $this->handle($form, $request);
    }
}
