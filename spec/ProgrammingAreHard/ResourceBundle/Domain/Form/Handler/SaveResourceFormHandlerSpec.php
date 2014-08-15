<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form\Handler;

use PhpSpec\ObjectBehavior;
use ProgrammingAreHard\ResourceBundle\Domain\Manager\ResourceManagerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Prophecy\Argument;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SaveResourceFormHandlerSpec extends ObjectBehavior
{
    function let(ResourceManagerInterface $manager)
    {
        $this->beConstructedWith($manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\Handler\SaveResourceFormHandler');
    }

    function it_throws_exception_if_form_does_not_contain_resource(
        FormInterface $form,
        ResourceManagerInterface $manager,
        Request $request
    ) {

        $manager->save(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow('ProgrammingAreHard\ResourceBundle\Domain\Form\Exception\ResourceFormDataException')->during('handle', array($form, $request));
    }

    function it_saves_to_repository_when_form_contains_resource(
        FormInterface $form,
        ResourceManagerInterface $manager,
        ResourceInterface $resource,
        Request $request
    ) {

        $form->getData()->willReturn($resource);

        $manager->save($resource)->shouldBeCalled();

        $this->handle($form, $request);
    }
}
