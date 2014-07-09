<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Transformer\ClassName;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TableizerTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Transformer\ClassName\TableizerTransformer');
    }

    function it_can_transform_class_names()
    {
        $this->transform('\ArrayObject')->shouldReturn('array_object');
        $this->transform('\stdClass')->shouldReturn('std_class');
    }

    function it_can_transform_the_same_class_twice()
    {
        $this->transform('\ArrayObject')->shouldReturn('array_object');
        $this->transform('\ArrayObject')->shouldReturn('array_object');
    }
}
