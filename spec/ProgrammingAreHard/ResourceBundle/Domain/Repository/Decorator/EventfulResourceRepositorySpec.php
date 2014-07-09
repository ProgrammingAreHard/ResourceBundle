<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Repository\Decorator;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvents;
use ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher\ResourceEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Prophecy\Argument;

class EventfulResourceRepositorySpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository, ResourceEventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($repository, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Repository\Decorator\EventfulResourceRepository');
    }

    function it_will_proxy_new_instance_call_to_repository($repository, ResourceInterface $resource)
    {
        $repository->newInstance()->willReturn($resource);

        $this->newInstance()->shouldReturn($resource);
    }

    function it_will_proxy_find_call_to_repository($repository, ResourceInterface $resource)
    {
        $repository->find($id = 2)->willReturn($resource);

        $this->find($id)->shouldReturn($resource);
    }

    function it_should_fire_pre_and_post_delete_events($repository, $dispatcher, ResourceInterface $resource)
    {
        $events = array(
            ResourceEvents::PRE_DELETE,
            ResourceEvents::POST_DELETE
        );
        $this->eventsShouldBeFired($events, $dispatcher, $resource);

        $repository->delete($resource)->shouldBeCalled();

        $this->delete($resource);
    }

    function it_should_fire_resource_creation_events_when_saving($repository, $dispatcher, ResourceInterface $resource)
    {
        $resource->isNew()->willReturn(true);

        $events = array(
            ResourceEvents::PRE_SAVE,
            ResourceEvents::PRE_CREATE,
            ResourceEvents::POST_CREATE,
            ResourceEvents::POST_SAVE
        );
        $this->eventsShouldBeFired($events, $dispatcher, $resource);

        $repository->save($resource)->shouldBeCalled();

        $this->save($resource);
    }

    function it_should_fire_resource_update_events_when_saving($repository, $dispatcher, ResourceInterface $resource)
    {
        $resource->isNew()->willReturn(false);

        $events = array(
            ResourceEvents::PRE_SAVE,
            ResourceEvents::PRE_UPDATE,
            ResourceEvents::POST_UPDATE,
            ResourceEvents::POST_SAVE
        );
        $this->eventsShouldBeFired($events, $dispatcher, $resource);

        $repository->save($resource)->shouldBeCalled();

        $this->save($resource);
    }

    private function eventsShouldBeFired(array $events, $dispatcher, $resource)
    {
        foreach ($events as $event) {
            $dispatcher->dispatch($event, $resource)->shouldBeCalled();
        }
    }
}
