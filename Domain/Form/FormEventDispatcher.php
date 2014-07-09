<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form;

use ProgrammingAreHard\ResourceBundle\Domain\Form\Event\FormEvent;
use ProgrammingAreHard\ResourceBundle\Domain\Form\Event\FormEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class FormEventDispatcher implements FormEventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(FormInterface $form, Request $request)
    {
        $this->dispatch(FormEvents::INITIALIZE, $form, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function invalid(FormInterface $form, Request $request)
    {
        $this->dispatch(FormEvents::INVALID, $form, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function preHandle(FormInterface $form, Request $request)
    {
        $this->dispatch(FormEvents::PRE_HANDLE, $form, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function postHandle(FormInterface $form, Request $request)
    {
        $this->dispatch(FormEvents::POST_HANDLE, $form, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function complete(FormInterface $form, Request $request)
    {
        $this->dispatch(FormEvents::COMPLETE, $form, $request);
    }

    /**
     * Dispatch a form event.
     *
     * @param string $name
     * @param FormInterface $form
     * @param Request $request
     */
    private function dispatch($name, FormInterface $form, Request $request)
    {
        $this->dispatcher->dispatch($form->getName() . '.' . $name, new FormEvent($form, $request));
    }
} 