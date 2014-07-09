<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator;

use ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventfulFormProcessor implements FormProcessorInterface
{
    /**
     * @var FormProcessorInterface
     */
    private $processor;

    /**
     * @var FormEventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param FormProcessorInterface $processor
     * @param FormEventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FormProcessorInterface $processor,
        FormEventDispatcherInterface $eventDispatcher
    ) {
        $this->processor = $processor;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FormInterface $form, Request $request)
    {
        $this->dispatcher->initialize($form, $request);

        $errors = $this->processor->process($form, $request);

        if (null === $errors) {
            $this->dispatcher->complete($form, $request);
        } else {
            $this->dispatcher->invalid($form, $request);
        }

        return $errors;
    }
}