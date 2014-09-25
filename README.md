ResourceBundle
==============

The ResourceBundle is an opinionated Symfony bundle to aid in developing REST APIs. It makes some
architectural decisions for you, allowing you to focus more on the domain of your application. It
uses as little magic as possible to make it easier to understand, debug, and extend.

---
##Prerequisites
The ResourceBundle relies on the [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/index.md)
to handle content negotiation and RESTful decoding of request bodies. After installing the bundle,
you must configure it before proceeding to use the ResourceBundle. [Here is a sample configuration](https://gist.github.com/dadamssg/a4f2784267f893ef9114)
to get started.

***Note***: *The ResourceBundle does not handle any sort of authentication. It is meant to be used in
conjunction with something like the [FOSOAuthServerBundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle).*

##Bundle Usage

- [Resources](#resources)
- [Resource Repositories](#resource-repositories)
- [Resource Managers](#resource-managers)
- [Resource Forms](#resource-forms)
- [Form Handlers](#form-handlers)
- [Form Processors](#form-processors)
- [Resource Controllers](#resource-controllers)
- [Resource Routing](#resource-routing)
- [Events](#events)
- [Bundle Configuration Reference](#bundle-configuration-reference)
- [Special Notes](#special-notes)

##Resources
The ResourceBundle is centered around resources. The bundle requires resources(entities) to implement
the very simple [ResourceInterface](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/ResourceInterface.php).
The examples assume you're using Doctrine but the bundle is ORM agnostic. First, let's create a simple resource.


```php
<?php // src/MyApp/CoreBundle/Entity/Task.php

namespace MyApp\CoreBundle\Entity;

use MyApp\CoreBundle\Domain\Task\TaskInterface;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

class Task implements TaskInterface, ResourceInterface
{
    protected $id;

    protected $task;

    public function getId()
    {
        return $this->id;
    }

    public function isNew()
    {
        return null === $this->getId();
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setTask($task)
    {
        $this->task = $task;
    }
}
```

##Resource Repositories
Once we have a resource, it's time to create a repository for the resource by implementing the [ResourceRepositoryInterface](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/Repository/ResourceRepositoryInterface.php).
If using Doctrine, just extend the bundled [BaseResourceRepository](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/Repository/Doctrine/BaseResourceRepository.php).

```php
<?php // src/MyApp/CoreBundle/Domain/Task/Repository/Doctrine/TaskRepository.php

namespace MyApp\CoreBundle\Domain\Task\Repository\Doctrine;

use MyApp\CoreBundle\Domain\Task\Repository\TaskRepositoryInterface;
use MyApp\CoreBundle\Entity\Task;
use ProgrammingAreHard\ResourceBundle\Domain\Repository\Doctrine\BaseResourceRepository;

class TaskRepository extends BaseResourceRepository implements TaskRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function newInstance()
    {
        return new Task;
    }
}
```

Register it with the container.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:
    myapp.task.repository:
        class: Doctrine\ORM\EntityRepository
        factory_service: doctrine.orm.entity_manager
        factory_method: getRepository
        arguments:
            - MyApp\CoreBundle\Entity\Task
```

Remember to update the mapping file.
```yaml
# src/MyApp/CoreBundle/Resources/doctrine/Task.orm.yml

MyApp\CoreBundle\Entity\Task:
    type: entity
    table: tasks
    repositoryClass: MyApp\CoreBundle\Domain\Task\Repository\Doctrine\TaskRepository
    id:
        id:
            type: integer
            generator:
                strategy: AUTO
    fields:
        task:
            type: string
            length: 255
```
##Resource Managers
Just like Doctrine, persisting and deleting resources is not done by repositories. With the ResourceBundle, this is done through a [ResourceManagerInterface](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/Manager/ResourceManagerInterface.php) implementation. If using Doctrine, you can use the bundled `ResourceManager`. Internally it uses Doctrine's `ManagerRegistry` to get the correct object manager for the resource.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    myapp.resource.manager:
        class: ProgrammingAreHard\ResourceBundle\Domain\Manager\Doctrine\ResourceManager
        arguments:
            - @doctrine
```


## Resource Forms
The bundle makes use of [Symfony's form component](http://symfony.com/doc/current/book/forms.html) to map incoming data to resources. Time to create a form for our `Task`.

```php
<?php // src/MyApp/CoreBundle/Domain/Task/Form/Type/TaskType.php

namespace MyApp\CoreBundle\Domain\Task\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('task');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
        ));
    }

    public function getName()
    {
        return 'task';
    }
}
```

By default, the bundle attempts to find a resource's form by looking for a form with the name of the resource's class
that has been lowercased and underscored. Ie. The bundle would expect to find a form by the name of `todo_list`
for a `MyApp/CoreBundle/Entity/TodoList` resource.

Let's register the form with the container.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

parameters:
    myapp.task.entity.class: MyApp\CoreBundle\Entity\Task

services:

    # other services...

    myapp.task.form.type:
        class: MyApp\CoreBundle\Domain\Task\Form\Type\TaskType
        arguments:
            - %myapp.task.entity.class%
        tags:
            - { name: form.type, alias: task }
```

## Form Handlers
Now that we have a resource, repository, and form it's time to create an implementation of a `FormHandlerInterface`.
Form handlers are only executed if a request was issued and the form was valid. The bundle comes with a `SaveResourceFormHandler`.
It extracts the data(the resource from the form) and saves it through a `ResourceManagerInterface`. Let's register a resource
form handler in the container.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    myapp.resource.form_handler:
        class: ProgrammingAreHard\ResourceBundle\Domain\Form\Handler\SaveResourceFormHandler
        arguments:
            - @myapp.resource.manager
```

##Form Processors
We need to use our new handler in a form processor.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    myapp.resource.form_processor:
        class: ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessor
        arguments:
            - @myapp.resource.form_handler
            - @pah_resource.form.error_extractor
```

Form processors use a form handler if the form is valid and an error extractor for when it is invalid. If you
do not like the default form error extractor, you can create your own by implementing the [FormErrorExtractorInterface](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/Form/FormErrorExtractorInterface.php).

##Resource Controllers
The glue that brings all these pieces together is the abstract [ResourceController](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Controller/ResourceController.php).
Let's create a concrete `TaskController`.

```php
<?php // src/MyApp/CoreBundle/Controller/TaskController.php

namespace MyApp\CoreBundle\Controller;

use MyApp\CoreBundle\Entity\Task;
use ProgrammingAreHard\ResourceBundle\Controller\ResourceController;
use ProgrammingAreHard\ResourceBundle\Domain\Event\ResourceEvents;
use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;

class TaskController extends ResourceController
{
    /**
     * Task class.
     *
     * @var string
     */
    protected $resourceClass = Task::CLASS; // using php 5.5's class constant

    /**
     * Show current user's tasks.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $tasks = $this->getUser()->getTasks();

        foreach ($tasks as $task) {
            $this->getResourceEventDispatcher()->dispatch(ResourceEvents::PRE_VIEW, $task);
        }

        return $this
            ->setData([$this->getPluralizedResourceName() => $tasks])
            ->respond();
    }

    /**
     * {@inheritdoc}
     */
    protected function getResourceLocation(ResourceInterface $resource)
    {
        return $this->generateUrl('my_app_task_view', ['id' => $resource->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormProcessor()
    {
        return $this->get('myapp.resource.form_processor');
    }

    /**
     * {@inheritdoc}
     */
    protected function getResourceManager()
    {
        return $this->get('myapp.resource.manager');
    }

    /**
     * {@inheritdoc}
     */
    protected function getResourceRepository()
    {
        return $this->get('myapp.task.repository');
    }
}
```
***Note***: *The `$resourceClass` is used by the `ResourceController` to find the relevant form, naming events, and serializing resources.*

***Tip***: *You might want to create your own base `ResourceController` and implement `::getFormProcessor()` and `::getResourceManager()` as they will probably be the same across each of your resource controllers.*

Because the `ResourceController` uses symfony's security component to check basic REST permissions, we need to implement
a security voter. You can customize this to suit your application's needs. For now, we're going to allow everything.

```php
<?php //src/MyApp/CoreBundle/Domain/Resource/Security/Voter/ResourceVoter.php

namespace MyApp\CoreBundle\Domain\Resource\Security\Voter;

use ProgrammingAreHard\ResourceBundle\Domain\ResourceInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ResourceVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return true
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $resource, array $attributes)
    {
        // Your application's logic to determine if the user has permission
        // to perform 'VIEW', 'CREATE', 'UPDATE', and/or 'DELETE' permissions.

        if ($resource instanceof ResourceInterface) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
```
Don't forget to register it.

```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    myapp.resource.security.voter:
        class: MyApp\CoreBundle\Domain\Resource\Security\Voter\ResourceVoter
        public: false
        tags:
            - { name: security.voter }
```

##Resource Routing
Once we have our `TaskController` and our security voter set up, we can then RESTfully route to actions.

```yaml
# app/config/routing.yml

# other routes...

myapp_tasks:
    resource: "@MyAppCoreBundle/Resources/config/routing.yml"
    prefix:   /api
```

```yaml
# src/MyApp/CoreBundle/Resources/config/routing.yml

myapp_task_create:
    pattern:  /tasks
    defaults: { _controller: MyAppCoreBundle:Task:create }
    methods:  [POST]

myapp_task_view_all:
    pattern:  /tasks
    defaults: { _controller: MyAppCoreBundle:Task:index }
    methods:  [GET]

myapp_task_view:
    pattern:  /tasks/{id}
    defaults: { _controller: MyAppCoreBundle:Task:show }
    methods:  [GET]

myapp_task_update:
    pattern:  /tasks/{id}
    defaults: { _controller: MyAppCoreBundle:Task:update }
    methods:  [PUT]

myapp_task_delete:
    pattern:  /tasks/{id}
    defaults: { _controller: MyAppCoreBundle:Task:delete }
    methods:  [DELETE]

```
I highly recommend you take a peek at the [ResourceController](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Controller/ResourceController.php)
to see what's happening under the hood.

By default, the ResourceBundle uses Symfony's serializer component to serialize resources for responses. However, I recommend using the [JMSSerializerBundle](https://github.com/schmittjoh/JMSSerializerBundle) for more flexibility.

##Events

The bundle's components are developed in a manner to make it easy to add functionality. One important piece of
functionality is the ability to dispatch events. Events can be dispatched by wrapping certain components in decorators.
The bundle comes with three.

###Eventful Resource Manager
You can use this decorator to dispatch events during manager interactions.

```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    myapp.resource.eventful_manager:
        class: ProgrammingAreHard\ResourceBundle\Domain\Manager\Decorator\EventfulResourceManager
        arguments:
            - @myapp.resource.manager
            - @pah_resource.resource.event_dispatcher
```

By using this decorator, the following events will be dispatched:

 - task.pre_save
 - task.post_save
 - task.pre_create
 - task.post_create
 - task.pre_update
 - task.post_update
 - task.pre_delete
 - task.post_delete

It uses the [ResourceEventDispatcher](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/EventDispatcher/ResourceEventDispatcher.php)
to dispatch these events. It uses the same class transformer as the ResourceController does when finding a resource's form.
It lowercases and underscores a resource's class to use in the event name. Feel free to use the resource event dispatcher
in your own code(like in your event listeners).

###Eventful Form Handler
You can decorate your form handlers to dispatch pre and post handle events.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    # redefine to use the eventful manager
    myapp.resource.form_handler:
        class: ProgrammingAreHard\ResourceBundle\Domain\Form\Handler\SaveResourceFormHandler
        arguments:
            - @myapp.resource.eventful_manager

    # redefine to use the above form handler
    myapp.resource.eventful_form_handler:
        class: ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormHandler
        arguments:
            - @myapp.resource.form_handler
            - @pah_resource.form.event_dispatcher

    # redefine to use the above eventful form handler
    myapp.resource.form_processor:
        class: ProgrammingAreHard\ResourceBundle\Domain\Form\FormProcessor
        arguments:
            - @myapp.resource.eventful_form_handler
            - @pah_resource.form.error_extractor
```

Using this decorator will dispatch the following events for the task's form handler:

- task.form.pre_handle
- task.form.post_handle

###Eventful Form Processor
You can also decorate form processors to dispatch certain events throughout the form processing.
```yaml
# src/MyApp/CoreBundle/Resources/services.yml

services:

    # other services...

    myapp.resource.eventful_form_processor:
        class: ProgrammingAreHard\ResourceBundle\Domain\Form\Decorator\EventfulFormProcessor
        arguments:
            - @myapp.resource.form_processor
            - @pah_resource.form.event_dispatcher
```

Using this decorator will dispatch the following events for the tasks's form.

- task.form.initialize
- task.form.invalid (only if the form is invalid)
- task.form.complete (only if the form is valid)

To summarize, by taking advantage of these decorators you will have access to the following events:

- task.pre_save
- task.post_save
- task.pre_create
- task.post_create
- task.pre_update
- task.post_update
- task.pre_delete
- task.post_delete
- task.form.initialize
- task.form.invalid
- task.form.pre_handle
- task.form.post_handle
- task.form.complete

Don't forget to use them in your `ResourceController`s though!

##Bundle Configuration Reference
This is the default bundle configuration.

```yaml
# app/config/config.yml

programming_are_hard_resource:
    class_transformer: pah_resource.class_name.underscore_transformer
    form_error_extractor: pah_resource.form.flattened_error_extractor
```

The `class_tranformer` is responsible for turning a resource's fully qualified class name into a name it uses when
finding a resource's form and dispatching events. It must implement [TransformerInterface](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/Transformer/TransformerInterface.php).

The `form_error_extractor` is responsbile for getting errors from a form. It must implement [FormErrorExtractorInterface](https://github.com/ProgrammingAreHard/ResourceBundle/blob/master/Domain/Form/FormErrorExtractorInterface.php)

##Special Notes
There are a few things to keep in mind when using this bundle.

###IndexAction
You might have noticed that there is no `indexAction` in the `ResourceController`. This is because the bundle can't reasonably guess all of the required parameters needed to come up with a generic enough solution. For example, most likely you will only want to display the current user's resources rather than everything in a resource repository. You may also want advanced url structures, ie. `/api/people/24/tasks`. The `indexAction` method signature we need to accommodate that person id wildcard. Consequently, pagination and filtering are left up to you.

###ResourceEvents::PRE_VIEW
This event is dispatched in the `create`, `show`, and `update` actions in the `ResourceController`. Depending on how you display related resources, you may want to ignore these events. You can use a serializer to display a resource's related resources. These resources would be fetched by calling getters on the resources themselves. Consequently, there is no place to dispatch the `ResourceEvents::PRE_VIEW` events for the related resources and still keep the bundle ORM agnostic. One option is to pull in the [BazingaHateoasBundle](https://github.com/willdurand/BazingaHateoasBundle) and [configure links](https://github.com/willdurand/Hateoas#configuring-links) for related resources. This way you can be sure that individual resources are only ever displayed directly by the `ResourceController`.
