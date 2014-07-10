<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Handler;

use ProgrammingAreHard\ResourceBundle\Domain\Form\Exception\ResourceFormDataException;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Manager\ResourceManagerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class SaveResourceFormHandler implements FormHandlerInterface
{
    /**
     * @var ResourceManagerInterface
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param ResourceManagerInterface $manager
     */
    public function __construct(ResourceManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form, Request $request)
    {
        $this->guardValidResource($resource = $form->getData());

        $this->manager->save($resource);
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