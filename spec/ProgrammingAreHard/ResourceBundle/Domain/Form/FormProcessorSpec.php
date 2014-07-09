<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormErrorExtractorInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormHandlerInterface;
use Prophecy\Argument;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormProcessorSpec extends ObjectBehavior
{
    public function let(
        FormHandlerInterface $formHandler,
        FormErrorExtractorInterface $errorExtractor
    ) {
        $this->beConstructedWith($formHandler, $errorExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessor');
    }

    function it_extracts_errors_when_invalid(
        FormHandlerInterface $formHandler,
        FormErrorExtractorInterface $errorExtractor,
        FormInterface $form,
        Request $request
    ) {
        $formHandler->handle($form, $request)->shouldNotBeCalled();

        $form->handleRequest($request)->shouldBeCalled();
        $form->isValid()->willReturn(false);

        $errorExtractor->extract($form)->willReturn($errors = array('foo' => 'bar'));

        $this->process($form, $request)->shouldReturn($errors);
    }

    function it_uses_handler_when_form_is_valid(
        FormHandlerInterface $formHandler,
        FormErrorExtractorInterface $errorExtractor,
        FormInterface $form,
        Request $request
    ) {
        $errorExtractor->extract($form)->shouldNotBeCalled();

        $form->handleRequest($request)->shouldBeCalled();
        $form->isValid()->willReturn(true);

        $formHandler->handle($form, $request)->shouldBeCalled();

        $this->process($form, $request)->shouldReturn(null);
    }
}
