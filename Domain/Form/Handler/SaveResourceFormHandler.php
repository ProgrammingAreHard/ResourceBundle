<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Handler;

use ProgrammingAreHard\ResourceBundle\Domain\Form\Exception\ResourceFormDataException;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class SaveResourceFormHandler implements FormHandlerInterface
{
    /**
     * @var ResourceRepositoryInterface
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param ResourceRepositoryInterface $repository
     */
    public function __construct(ResourceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form, Request $request)
    {
        $this->guardValidResource($resource = $form->getData());

        $this->repository->save($resource);
    }

    /**
     * Ensure a resource is present.
     *
     * @param $resource
     * @throws ResourceFormDataException
     */
    private function guardValidResource($resource = null)
    {
        if (!$resource instanceof ResourceInterface) {
            throw new ResourceFormDataException(sprintf(
                'Invalid form data. Expecting ResourceInterface but got "%s".',
                is_object($resource) ? get_class($resource) : gettype($resource)
            ));
        }
    }
} 