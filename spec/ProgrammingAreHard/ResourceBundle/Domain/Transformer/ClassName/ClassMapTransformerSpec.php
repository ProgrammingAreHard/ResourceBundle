<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Transformer\ClassName;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClassMapTransformerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            array('foo' => 'bar')
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Transformer\ClassName\ClassMapTransformer');
    }

    function it_can_get_a_mapped_value()
    {
        $this->transform('foo')->shouldReturn('bar');
    }

    function it_throws_exception_when_attempting_to_get_unmapped_value()
    {
        $this->shouldThrow('\RunTimeException')->during('transform', array('baz'));
    }
}
