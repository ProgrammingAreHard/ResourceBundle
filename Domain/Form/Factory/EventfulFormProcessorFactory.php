<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Factory;

use ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormHandler;
use ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormProcessor;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormErrorExtractorInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessor;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessorInterface;

class EventfulFormProcessorFactory
{
    /**
     * @var FormEventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var FormErrorExtractorInterface
     */
    private $errorExtractor;

    /**
     * Constructor.
     *
     * @param FormEventDispatcherInterface $dispatcher
     * @param FormErrorExtractorInterface $errorExtractor
     */
    public function __construct(FormEventDispatcherInterface $dispatcher, FormErrorExtractorInterface $errorExtractor)
    {
        $this->dispatcher = $dispatcher;
        $this->errorExtractor = $errorExtractor;
    }

    /**
     * Create an eventful form processor with an eventful form handler.
     *
     * @param FormHandlerInterface $handler
     * @return FormProcessorInterface
     */
    public function create(FormHandlerInterface $handler)
    {
        $eventfulHandler = new EventfulFormHandler($handler, $this->dispatcher);
        $processor = new FormProcessor($eventfulHandler, $this->errorExtractor);
        return new EventfulFormProcessor($processor, $this->dispatcher);
    }
} 