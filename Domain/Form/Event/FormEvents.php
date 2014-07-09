<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Form\Event;

final class FormEvents
{
    const INITIALIZE = 'form.initialize';
    const INVALID = 'form.invalid';
    const PRE_HANDLE = 'form.pre_handle';
    const POST_HANDLE = 'form.post_handle';
    const COMPLETE = 'form.complete';
} 