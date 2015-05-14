<?php

namespace ProgrammingAreHard\ResourceBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use ProgrammingAreHard\ResourceBundle\DependencyInjection\ProgrammingAreHardResourceExtension as ResourceExtension;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiController extends FOSRestController
{
    /**
     * @var int
     */
    protected $statusCode = Codes::HTTP_OK;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Add a header.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    protected function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set the errors.
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Set the data to respond with.
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Respond with set data.
     *
     * @return Response
     */
    protected function respond()
    {
        $data = $this->data;

        if ($this->errors) {
            $errors = array_key_exists('errors', $data) ? $data['errors'] : array();
            $data['errors'] = array_merge($this->errors, $errors);
        }

        $view = $this->view($data, $this->statusCode);

        foreach ($this->headers as $key => $value) {
            $view->setHeader($key, $value);
        }

        return $this->handleView($view);
    }

    /**
     * Respond with form errors.
     *
     * @param FormInterface $form
     * @return Response
     */
    protected function respondWithForm(FormInterface $form)
    {
        $errors = $this->getFormErrorExtractor()->extract($form);

        return $this
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setErrors($errors)
            ->respond();
    }

    /**
     * Response with no content.
     *
     * @return Response
     */
    protected function noContent()
    {
        return $this->handleView($this->view(null, Codes::HTTP_NO_CONTENT));
    }

    /**
     * Set the location header.
     *
     * @param $url
     * @return $this
     */
    protected function setLocationHeader($url)
    {
        $this->addHeader('Location', $url);
        return $this;
    }

    /**
     * Dispatch an event.
     *
     * @param $name
     * @param Event $event
     */
    protected function dispatch($name, Event $event)
    {
        $this->get('event_dispatcher')->dispatch($name, $event);
    }

    /**
     * Throw exception if not granted permission.
     *
     * @param string $permission
     * @param object $resource
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function grantOr403($permission, $resource)
    {
        if (false === $this->get('security.context')->isGranted($permission, $resource)) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    }

    /**
     * Get the form error extractor.
     *
     * @return \ProgrammingAreHard\ResourceBundle\Domain\Form\FormErrorExtractorInterface
     */
    protected function getFormErrorExtractor()
    {
        return $this->get(ResourceExtension::FORM_ERROR_EXTRACTOR_ID );
    }
}
