<?php

namespace spec\ProgrammingAreHard\ResourceBundle\Domain\Form;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FlattenedFormErrorExtractorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('ProgrammingAreHard\ResourceBundle\Domain\Form\FlattenedFormErrorExtractor');
    }

    function it_can_extract_errors(
        FormInterface $form1,
        FormInterface $form2,
        FormError $error1,
        FormError $error2,
        FormError $error3
    ) {
        $form1->isValid()->willReturn(false);
        $form2->isValid()->willReturn(false);
        $form1->all()->willReturn(array($form2));
        $form2->all()->willReturn(array());

        $errors = array(
            'Error 1',
            'Error 2',
            'Error 3'
        );

        $error1->getMessage()->willReturn($errors[0]);
        $error2->getMessage()->willReturn($errors[1]);
        $error3->getMessage()->willReturn($errors[2]);

        $form1->getErrors()->willReturn(array($error1));
        $form2->getErrors()->willReturn(array($error2, $error3));

        $this->extract($form1)->shouldReturn($errors);
    }
}
