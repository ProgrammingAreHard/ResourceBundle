<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormProcessorInterface
{
    /**
     * Process the form and return a resource.
     *
     * @param FormInterface $form
     * @param Request $request
     * @return string[]|null - array of errors or null if none
     */
    public function process(FormInterface $form, Request $request);
}