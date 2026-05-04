# Callbacks and CallbackAnnotationTasks

This is a separate annotations tool that focuses on **methods** and their doc
blocks instead of classes. By default it ships with:

- `TableCallbackAnnotatorTask`

## Table Callback Annotations

Behaviors and generic code use the following signature:

```php
/**
 * @param \Cake\Event\EventInterface $event Event
 * @param \Cake\Datasource\EntityInterface $entity Entity
 * @param \ArrayObject $options Options
 * @return void
 */
public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
```

As long as you only use methods and attributes of the `EntityInterface`
contract, this is fine.

But in specific Table-class code, you usually also access the entity's
concrete properties. There the type hint is somewhat a lie. To please the IDE
and tooling like PHPStan we can at least fix up the doc block — and that is
what this task does, declaring the `Post` entity to be available and used
inside.

Inside the concrete `PostsTable` after running the `callbacks` command:

```php
/**
 * @param \Cake\Event\EventInterface $event Event
 * @param \App\Model\Entity\Post $entity Entity
 * @param \ArrayObject $options Options
 * @return void
 */
public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
```

## Entity Virtual Field Setter/Getter Annotations

A virtual field will be "linked" to the property it handles:

```php
/**
 * @see \App\Model\Entity\MyEntity::$expected_type
 *
 * @return int|null
 */
protected function _getExpectedType(): ?int
```

This way you can quick-jump from the property to the getter and vice versa
within your IDE.

## Custom Tasks

Create your own task class:

```php
namespace App\Annotator\CallbackAnnotatorTask;

use IdeHelper\Annotator\CallbackAnnotatorTask\AbstractCallbackAnnotatorTask;
use IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface;

class MyCallbackAnnotatorTask extends AbstractCallbackAnnotatorTask implements CallbackAnnotatorTaskInterface {

    /**
     * @param string $path
     * @return bool
     */
    public function shouldRun(string $path): bool {
        // ...
    }

    /**
     * @param string $path
     * @return bool
     */
    public function annotate(string $path): bool {
        // ...
    }

}
```

Then add it to the config:

```php
'IdeHelper' => [
    'CallbackAnnotatorTasks' => [
        'MyCallbackAnnotatorTask' => \App\Annotator\CallbackAnnotatorTask\MyCallbackAnnotatorTask::class,
    ],
],
```

The key `'MyCallbackAnnotatorTask'` can be any string.

Replacing existing tasks works the same way as for classes: use the native
class name as key, your replacement as value, or `null` to disable.
