<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Manager\Decorator;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvents;
use ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher\ResourceEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Manager\ResourceManagerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Prophecy\Argument;

class EventfulResourceManagerSpec extends ObjectBehavior
{
    public function let(ResourceManagerInterface $manager, ResourceEventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($manager, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Manager\Decorator\EventfulResourceManager');
    }

    function it_should_fire_pre_and_post_delete_events(
        ResourceManagerInterface $manager,
        ResourceEventDispatcherInterface $dispatcher,
        ResourceInterface $resource
    ) {
        $dispatcher->dispatch(ResourceEvents::PRE_DELETE, $resource)->shouldBeCalled();
        $dispatcher->dispatch(ResourceEvents::POST_DELETE, $resource)->shouldNotBeCalled();

        $dispatcher->dispatch(ResourceEvents::PRE_DELETE, $resource)->will(
            function () use ($dispatcher, $resource) {
                $dispatcher->dispatch(ResourceEvents::POST_DELETE, $resource)->shouldBeCalled();
            }
        );

        $manager->delete($resource)->shouldBeCalled();

        $this->delete($resource);
    }

    function it_should_fire_resource_creation_events_when_saving(
        ResourceManagerInterface $manager,
        ResourceEventDispatcherInterface $dispatcher,
        ResourceInterface $resource
    ) {
        $resource->isNew()->willReturn(true);

        $events = array(
            ResourceEvents::PRE_SAVE,
            ResourceEvents::PRE_CREATE,
            ResourceEvents::POST_CREATE,
            ResourceEvents::POST_SAVE
        );
        $this->eventsShouldBeFired($events, $dispatcher, $resource);

        $manager->save($resource)->shouldBeCalled();

        $this->save($resource);
    }

    function it_should_fire_resource_update_events_when_saving(
        ResourceManagerInterface $manager,
        ResourceEventDispatcherInterface $dispatcher,
        ResourceInterface $resource
    ) {
        $resource->isNew()->willReturn(false);

        $events = array(
            ResourceEvents::PRE_SAVE,
            ResourceEvents::PRE_UPDATE,
            ResourceEvents::POST_UPDATE,
            ResourceEvents::POST_SAVE
        );
        $this->eventsShouldBeFired($events, $dispatcher, $resource);

        $manager->save($resource)->shouldBeCalled();

        $this->save($resource);
    }

    private function eventsShouldBeFired(
        array $events,
        ResourceEventDispatcherInterface $dispatcher,
        ResourceInterface $resource
    ) {
        foreach ($events as $event) {
            $dispatcher->dispatch($event, $resource)->shouldBeCalled();
        }
    }
}
