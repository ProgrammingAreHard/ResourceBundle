<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Manager\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use ProgrammingAreHard\ResourceBundle\Domain\Manager\ResourceManagerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

final class ResourceManager implements ResourceManagerInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ResourceInterface $resource, $andFlush = true)
    {
        $manager = $this->getObjectManager($resource);
        $manager->persist($resource);

        if ($andFlush) {
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(ResourceInterface $resource, $andFlush = true)
    {
        $manager = $this->getObjectManager($resource);
        $manager->remove($resource);

        if ($andFlush) {
            $manager->flush();
        }
    }

    /**
     * Get the resource's object manager.
     *
     * @param ResourceInterface $resource
     * @return ObjectManager
     */
    private function getObjectManager(ResourceInterface $resource)
    {
        return $this->managerRegistry->getManagerForClass(get_class($resource));
    }
}