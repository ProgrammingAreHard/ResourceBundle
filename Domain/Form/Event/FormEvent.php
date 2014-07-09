<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormEvent extends Event
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Request
     */
    private $request;

    /**
     * Constructor.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(FormInterface $form, Request $request)
    {
        $this->form = $form;
        $this->request = $request;
    }

    /**
     * Get the form.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get the request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
} 