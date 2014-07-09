<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class FormProcessor implements FormProcessorInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;

    /**
     * @var FormErrorExtractorInterface
     */
    private $errorExtractor;

    /**
     * Constructor.
     *
     * @param FormHandlerInterface $formHandler
     * @param FormErrorExtractorInterface $errorExtractor
     */
    public function __construct(
        FormHandlerInterface $formHandler,
        FormErrorExtractorInterface $errorExtractor
    ) {
        $this->formHandler = $formHandler;
        $this->errorExtractor = $errorExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->errorExtractor->extract($form);
        }

        $this->formHandler->handle($form, $request);

        return null;
    }
}