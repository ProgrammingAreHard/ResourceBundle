<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Transformer\TransformerInterface;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResourceEventDispatcherSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher, TransformerInterface $classNameTransformer)
    {
        $this->beConstructedWith($dispatcher, $classNameTransformer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher\ResourceEventDispatcher');
    }

    function it_can_dispatch_an_event(
        EventDispatcherInterface $dispatcher,
        TransformerInterface $classNameTransformer,
        ResourceInterface $resource
    ) {
        $eventName = 'pre_save';
        $resourceName = 'resource';

        $classNameTransformer->transform(Argument::type('string'))->willReturn($resourceName);

        $resourceEventClass = 'ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvent';

        $resourceEvent = Argument::type($resourceEventClass);

        $dispatcher->dispatch("$resourceName.$eventName", $resourceEvent)->shouldBeCalled();

        $this->dispatch($eventName, $resource)->shouldReturnAnInstanceOf($resourceEventClass);
    }
}
