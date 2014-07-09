<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Repository\Doctrine;

use Doctrine\ORM\EntityRepository;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;

abstract class BaseResourceRepository extends EntityRepository implements ResourceRepositoryInterface
{
    /**
     * Save a resource.
     *
     * @param Resource $resource
     */
    public function save(ResourceInterface $resource, $andFlush = true)
    {
        $this->getEntityManager()->persist($resource);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * {@inheritDoc}
     */
    public function delete(ResourceInterface $resource, $andFlush = true)
    {
        $this->getEntityManager()->remove($resource);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }
} 