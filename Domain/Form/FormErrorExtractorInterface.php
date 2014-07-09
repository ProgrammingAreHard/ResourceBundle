<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form;

use Symfony\Component\Form\FormInterface;

interface FormErrorExtractorInterface
{
    /**
     * Extract the errors from the form.
     *
     * @param FormInterface $form
     * @return string[]
     */
    public function extract(FormInterface $form);
} 