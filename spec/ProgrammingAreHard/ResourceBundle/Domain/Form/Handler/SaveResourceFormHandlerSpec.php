<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form\Handler;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Prophecy\Argument;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SaveResourceFormHandlerSpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\Handler\SaveResourceFormHandler');
    }

    function it_throws_exception_if_form_does_not_contain_resource(
        FormInterface $form,
        ResourceRepositoryInterface $repository,
        Request $request
    ) {

        $repository->save(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow('\RunTimeException')->during('handle', array($form, $request));
    }

    function it_saves_to_repository_when_form_contains_resource(
        FormInterface $form,
        ResourceRepositoryInterface $repository,
        ResourceInterface $resource,
        Request $request
    ) {

        $form->getData()->willReturn($resource);

        $repository->save($resource)->shouldBeCalled();

        $this->handle($form, $request);
    }
}
