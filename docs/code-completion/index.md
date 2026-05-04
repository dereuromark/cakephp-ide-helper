# Code Completion File Generator

In contrast to the [PhpStorm meta file generator](/generator/), this tool is
intentionally generic and IDE agnostic.

![Behavior Code Completion](/img/code_completion.png)

## Usage

This command will generate the CodeCompletion `php` files into your app's
`TMP/` directory:

```bash
bin/cake generate code_completion
```

The files should not be persisted — they will always be regenerated or
updated locally if needed.

::: tip composer hook
Set up a `post-install-cmd` hook for composer to keep them up to date
automatically.
:::

## Behaviors

```php
/** @var \Search\Manager $searchManager */
$searchManager = $this->behaviors()->Search->searchManager();
```

So far `$searchManager` required the annotation above to be type-hinted and
clickable, because the magic property access is not resolvable on its own.

With the generated code completion file this is no longer necessary. The
property `Search` is detected as `\Search\Model\Behavior\SearchBehavior`,
making `searchManager()` available in the IDE for method argument checking
and following.

## SelectQuery generics

The code completion generator also ships a `Cake\ORM\Query\SelectQuery`
helper stub for fluent query chains. This is especially useful together with
the model annotation option:

```php
'IdeHelper' => [
    'tableEntityQuery' => true,
],
```

That combination lets IDEs preserve the concrete entity type through calls
such as:

```php
$query = $this->Users->find();
$query->where(['active' => true])->all();
```

The generated stub intentionally focuses on methods where the subject type is
stable or where Cake already has a clear subject transition:

| Flow | Semantic result | ide-helper support |
| --- | --- | --- |
| `$this->Users->find()->where(...)->all()` | `User` entities | Covered via `tableEntityQuery` + `SelectQuery<TSubject>` stub |
| `$this->Users->find('active')->matching('Roles')->contain('Profiles')->all()` | `User` entities | Covered; these fluent methods preserve `TSubject` |
| `$this->Users->find()->disableHydration()->all()` | `array<string, mixed>` rows | Covered; `disableHydration()` switches the query stub to array results |
| `$this->Users->find('list')->all()` | non-entity shaped result | Not forced by default; keep this outside the entity-query assumption |
| `$this->Users->find()->formatResults(...)` | depends on formatter | Not modeled; formatter callbacks can reshape results arbitrarily |
| `$this->Users->find()->mapReduce(...)` | depends on mapper/reducer | Not modeled; map/reduce can reshape results arbitrarily |

This keeps the default helper honest: preserve the type where the query
stays subject-compatible, and avoid pretending that formatter-driven or
list-style flows are still plain entity queries.

For PhpStorm projects you can point the generated code completion files into
`.phpstorm.meta.php/` so they are indexed as local project helpers:

```php
'IdeHelper' => [
    'codeCompletionPath' => ROOT . DS . '.phpstorm.meta.php' . DS,
],
```

Then regenerate the files with:

```bash
bin/cake generate code_completion
```

## Adding Your Own Tasks

Create your own task class:

```php
namespace App\CodeCompletion\Task;

use IdeHelper\CodeCompletion\Task\TaskInterface;

class MyTask implements TaskInterface {

    const TYPE_NAMESPACE = 'Some\Namespace';

    /**
     * @return string
     */
    public function type(): string {
        return static::TYPE_NAMESPACE;
    }

    /**
     * @return string
     */
    public function create(): string {
        // ...
    }

}
```

Then add it to the config:

```php
'IdeHelper' => [
    'codeCompletionTasks' => [
        'MyTask' => \App\CodeCompletion\Task\MyTask::class,
    ],
],
```

The key `'MyTask'` can be any string.

### Replacing native tasks

Using associative arrays you can swap any native task with your own
implementation:

```php
'IdeHelper' => [
    'codeCompletionTasks' => [
        \IdeHelper\CodeCompletion\Task\BehaviorTask::class => \App\CodeCompletion\Task\MyEnhancedBehaviorTask::class,
    ],
],
```

The native class name is the key, your replacement the value. Setting the
value to `null` disables a native task entirely.

### Property example

So let's imagine you have the following magic properties you want to annotate:

```php
$alpha = $someObject->Alpha; // Returns \My\Cool\Alpha class
$beta = $someObject->Beta; // Returns \My\Cool\Beta class
```

Then make sure your task's `create()` method returns something like:

```php
abstract class SomeObject extends SomeObjectInterface {

    /**
     * Alpha class.
     *
     * @var \My\Cool\Alpha
     */
    public $Alpha;

    /**
     * Beta class.
     *
     * @var \My\Cool\Beta
     */
    public $Beta;

}
```

We use the `abstract` keyword to avoid direct implementation hinting.

### Method example

Let's imagine you have the following magic methods you want to annotate:

```php
$alpha = $someObject->alpha(); // Returns \My\Cool\Alpha class
$beta = $someObject->beta(); // Returns \My\Cool\Beta class
```

Then make sure your task's `create()` method returns something like:

```php
abstract class SomeObject extends SomeObjectInterface {

    /**
     * Alpha class.
     *
     * @var \My\Cool\Alpha
     */
    protected $alpha;

    // ...

    /**
     * @return \My\Cool\Alpha
     */
    public function search(): Alpha {
        return $this->alpha;
    }

    // ...

}
```

## Custom Path for Files

Using the Configure key `'IdeHelper.codeCompletionPath'` you can use a custom
path in your project. This way the files can be added to version control.
