<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormHandlerInterface
{
    /**
     * Handle the form.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function handle(FormInterface $form, Request $request);
} 