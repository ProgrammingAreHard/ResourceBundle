<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator;

use ProgrammingAreHard\ResourceBundle\Domain\Form\FormEventDispatcherInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventfulFormHandler implements FormHandlerInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $handler;

    /**
     * @var FormEventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param FormHandlerInterface $handler
     * @param FormEventDispatcherInterface $dispatcher
     */
    public function __construct(FormHandlerInterface $handler, FormEventDispatcherInterface $dispatcher)
    {
        $this->handler = $handler;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form, Request $request)
    {
        $this->dispatcher->preHandle($form, $request);
        $this->handler->handle($form, $request);
        $this->dispatcher->postHandle($form, $request);
    }
} 