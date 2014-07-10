<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Repository\Doctrine;

use Doctrine\ORM\EntityRepository;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;

abstract class BaseResourceRepository extends EntityRepository implements ResourceRepositoryInterface
{
} 