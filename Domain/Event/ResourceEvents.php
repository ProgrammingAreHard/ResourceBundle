<?php

namespace ProgrammingAreHard\ResourceBundle\Domain\Event;

final class ResourceEvents
{
    const PRE_SAVE = 'pre_save';
    const POST_SAVE = 'post_save';
    const PRE_CREATE = 'pre_create';
    const POST_CREATE = 'post_create';
    const PRE_UPDATE = 'pre_update';
    const POST_UPDATE = 'post_update';
    const PRE_DELETE = 'pre_delete';
    const POST_DELETE = 'post_delete';
    const PRE_VIEW = 'pre_view';
} 