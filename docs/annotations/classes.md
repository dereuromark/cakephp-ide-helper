# Classes and ClassAnnotationTasks

In order to run certain "fixers" over all classes, class annotations and their
tasks are available. Out of the box the following tasks are run.

## ModelAware

Any `use ModelAwareTrait` together with `$this->loadModel(...)` calls will add
the required annotation on top of the class.

## `Form::execute()`

Adds a convenience inline annotation so you can quickly jump to the actual
business logic:

```php
use App\Form\ReleaseForm;

$releaseForm = new ReleaseForm();

/** @uses \App\Form\ReleaseForm::_execute() */
$releaseForm->execute($data);
```

## `Mailer::send()`

Adds a convenience inline annotation so you can quickly jump to the actual
business logic:

```php
use App\Mailer\NotificationMailer;
// or
$notificationMailer = $this->getMailer('Notification');

/** @uses \App\Mailer\NotificationMailer::notify() */
$notificationMailer->send('notify', [$user, $details]);
```

## Test

Test classes of specific types can be annotated with the corresponding class
they test. This is mainly useful for the following types, which are invoked
indirectly via the integration test harness:

- Controller
- Command

The `@link` statement helps to quick-jump to the class if needed. If your test
class already has a `#[UsesClass(...)]` attribute, no annotation will be
added.

::: tip Use `@uses` instead of `@link`
Set the `IdeHelper.preferLinkOverUsesInTests` config key to `false` to use
`@uses` instead of `@link`.
:::

## Custom Tasks

Create your own task class:

```php
namespace App\Annotator\ClassAnnotatorTask;

use IdeHelper\Annotator\ClassAnnotatorTask\AbstractClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\ClassAnnotatorTaskInterface;

class MyClassAnnotatorTask extends AbstractClassAnnotatorTask implements ClassAnnotatorTaskInterface {

    /**
     * @param string $path
     * @param string $content
     * @return bool
     */
    public function shouldRun(string $path, string $content): bool {
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
    'classAnnotatorTasks' => [
        'MyClassAnnotatorTask' => \App\Annotator\ClassAnnotatorTask\MyClassAnnotatorTask::class,
    ],
],
```

The key `'MyClassAnnotatorTask'` can be any string.

For a fully worked PHP-AST example, see [Custom Class Annotators](./custom-class-annotator).

## Targeting Custom Directories

By default `bin/cake annotate classes` walks `src/` (app + plugin classpaths)
and `tests/TestCase/` (when `TestClassAnnotatorTask` is registered). A custom
task whose subjects live elsewhere — for example test-fixture factories under
`tests/Factory/`, scenario classes, or generated stubs — can opt into having
those directories walked by also implementing
`PathAwareClassAnnotatorTaskInterface`:

```php
namespace App\Annotator\ClassAnnotatorTask;

use IdeHelper\Annotator\ClassAnnotatorTask\AbstractClassAnnotatorTask;
use IdeHelper\Annotator\ClassAnnotatorTask\PathAwareClassAnnotatorTaskInterface;

class MyClassAnnotatorTask extends AbstractClassAnnotatorTask implements PathAwareClassAnnotatorTaskInterface {

    /**
     * @return array<string>
     */
    public static function scanPaths(): array {
        return ['tests/Factory/'];
    }

    public function shouldRun(string $path, string $content): bool { /* ... */ }
    public function annotate(string $path): bool { /* ... */ }

}
```

Paths are project-root relative for app context, plugin-root relative when
run with `-p`. They are walked recursively. Paths that do not exist on disk
are silently skipped, and a path declared by multiple tasks is walked only
once.

::: tip Path convention
Return paths with forward slashes and a trailing slash (e.g.
`'tests/Factory/'`), independent of OS. The command normalizes to the
OS-native separator before walking, so the dedup key stays stable across
Windows / \*nix and across tasks that disagree on style.
:::

The interface is optional and additive — existing tasks that do not implement
it behave unchanged. The feature is opt-in: a path-aware task is only
consulted when it is registered in `IdeHelper.classAnnotatorTasks`.

## Replacing Native Tasks

Using associative arrays you can swap any native task with your own
implementation:

```php
'IdeHelper' => [
    'classAnnotatorTasks' => [
        \IdeHelper\Annotator\ClassAnnotatorTask\ModelAwareTask::class => \App\Annotator\ClassAnnotatorTask\MyEnhancedModelAwareTask::class,
    ],
],
```

The native class name is the key, your replacement the value. Setting the
value to `null` disables a native task entirely.
