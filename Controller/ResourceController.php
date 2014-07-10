<?php

namespace ProgrammingAreHard\ResourceBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use FOS\RestBundle\Util\Codes;
use ProgrammingAreHard\ResourceBundle\DependencyInjection\ProgrammingAreHardResourceExtension as ResourceExtension;
use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvents;
use ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessorInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Manager\ResourceManagerInterface;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\ResourceRepositoryInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class ResourceController extends ApiController
{
    /**
     * @var string
     */
    protected $resourceClass;

    /**
     * Create a resource.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $resource = $this->getResourceRepository()->newInstance();

        $this->grantOr403('CREATE', $resource);

        $form = $this->createForm($this->getFormName(), $resource);

        if ($response = $this->processForm($form, $request)) {
            return $response;
        }

        $this->getResourceEventDispatcher()->dispatch(ResourceEvents::PRE_VIEW, $resource);

        $url = $this->getResourceLocation($resource);

        return $this
            ->setStatusCode(Codes::HTTP_CREATED)
            ->setLocationHeader($url)
            ->setResource($resource)
            ->respond();
    }

    /**
     * Show a single resource.
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction($id)
    {
        $resource = $this->findOr404($id);

        $this->grantOr403('VIEW', $resource);

        $this->getResourceEventDispatcher()->dispatch(ResourceEvents::PRE_VIEW, $resource);

        return $this
            ->setResource($resource)
            ->respond();
    }

    /**
     * Update a resource.
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateAction(Request $request, $id)
    {
        $resource = $this->findOr404($id);

        $this->grantOr403('EDIT', $resource);

        $form = $this->createForm($this->getFormName(), $resource, array('method' => 'PUT'));

        if ($response = $this->processForm($form, $request)) {
            return $response;
        }

        $this->getResourceEventDispatcher()->dispatch(ResourceEvents::PRE_VIEW, $resource);

        return $this
            ->setStatusCode(Codes::HTTP_OK)
            ->setResource($resource)
            ->respond();
    }

    /**
     * Delete a resource.
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $resource = $this->findOr404($id);

        $this->grantOr403('DELETE', $resource);

        $this->getResourceManager()->delete($resource);

        return $this->noContent();
    }

    /**
     * Process the form.
     *
     * @param FormInterface $form
     * @param Request $request
     * @return Response|null
     */
    protected function processForm(FormInterface $form, Request $request)
    {
        $processor = $this->getFormProcessor();

        if ($errors = $processor->process($form, $request)) {
            return $this
                ->setStatusCode(Codes::HTTP_BAD_REQUEST)
                ->setErrors($errors)
                ->respond();
        }

        return null;
    }

    /**
     * Find resource or 404.
     *
     * @param $id
     * @return ResourceInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findOr404($id)
    {
        if (!$resource = $this->getResourceRepository()->find($id)) {
            throw new NotFoundHttpException('Resource not found.');
        }

        return $resource;
    }

    /**
     * Set the resource to serialize.
     *
     * @param ResourceInterface $resource
     * @return $this
     */
    protected function setResource(ResourceInterface $resource)
    {
        return $this->setData(array($this->getResourceName() => $resource));
    }

    /**
     * Get the form name.
     *
     * @return string
     */
    protected function getFormName()
    {
        return $this->getResourceName();
    }

    /**
     * Get the resource short name.
     *
     * @return string
     */
    protected function getResourceName()
    {
        $classTransformer = $this->get(ResourceExtension::RESOURCE_CLASS_NAME_TRANSFORMER_ID);
        return $classTransformer->transform($this->getResourceClass());
    }

    /**
     * Get the resource pluralized name.
     *
     * @return string
     */
    protected function getPluralizedResourceName()
    {
        return Inflector::pluralize($this->getResourceName());
    }

    /**
     * Get the resource class.
     *
     * @return string
     */
    protected function getResourceClass()
    {
        if (!$this->resourceClass) {
            throw new \RunTimeException(sprintf('A %s::$resourceClass property must be defined.', get_class($this)));
        }

        return $this->resourceClass;
    }

    /**
     * Get the resource event dispatcher.
     *
     * @return \ProgrammingAreHard\ResourceBundle\Domain\EventDispatcher\ResourceEventDispatcher
     */
    protected function getResourceEventDispatcher()
    {
        return $this->get(ResourceExtension::RESOURCE_EVENT_DISPATCHER_ID);
    }

    /**
     * Get the resource's location.
     *
     * @param ResourceInterface $resource
     * @return string - resource's url
     */
    abstract protected function getResourceLocation(ResourceInterface $resource);

    /**
     * Get the form processor.
     *
     * @return FormProcessorInterface
     */
    abstract protected function getFormProcessor();

    /**
     * Get the resource repository.
     *
     * @return ResourceRepositoryInterface
     */
    abstract protected function getResourceRepository();

    /**
     * Get the resource manager.
     *
     * @return ResourceManagerInterface
     */
    abstract protected function getResourceManager();
} 