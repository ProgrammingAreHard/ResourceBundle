<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FormEventDispatcherInterface
{
    /**
     * Dispatch the initialize form event.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function initialize(FormInterface $form, Request $request);

    /**
     * Dispatch the invalid form event.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function invalid(FormInterface $form, Request $request);

    /**
     * Dispatch the pre-handle form event.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function preHandle(FormInterface $form, Request $request);

    /**
     * Dispatch the post-handle form event.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function postHandle(FormInterface $form, Request $request);

    /**
     * Dispatch the completion form event.
     *
     * @param FormInterface $form
     * @param Request $request
     */
    public function complete(FormInterface $form, Request $request);
} 