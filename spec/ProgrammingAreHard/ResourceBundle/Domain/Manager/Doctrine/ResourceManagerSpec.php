<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Manager\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Prophecy\Argument;

class ResourceManagerSpec extends ObjectBehavior
{
    public function let(ManagerRegistry $managerRegistry)
    {
        $this->beConstructedWith($managerRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Manager\Doctrine\ResourceManager');
    }

    function it_uses_object_manager_to_persist_and_flush_when_saving(
        ManagerRegistry $managerRegistry,
        ObjectManager $objectManager,
        ResourceInterface $resource
    ) {
        $objectManager->persist($resource)->shouldBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $managerRegistry->getManagerForClass(Argument::type('string'))->willReturn($objectManager);

        $objectManager->persist($resource)->will(
            function () use ($objectManager) {
                $objectManager->flush()->shouldBeCalled();
            }
        );

        $this->save($resource);
    }

    function it_uses_object_manager_to_remove_and_flush_when_deleting(
        ManagerRegistry $managerRegistry,
        ObjectManager $objectManager,
        ResourceInterface $resource
    ) {
        $objectManager->remove($resource)->shouldBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $managerRegistry->getManagerForClass(Argument::type('string'))->willReturn($objectManager);

        $objectManager->remove($resource)->will(
            function () use ($objectManager) {
                $objectManager->flush()->shouldBeCalled();
            }
        );

        $this->delete($resource);
    }
}
