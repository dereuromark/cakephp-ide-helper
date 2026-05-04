# Custom Tasks and Directives

## Adding Your Own Tasks

Create your own task class:

```php
namespace App\Generator\Task;

use IdeHelper\Generator\Task\TaskInterface;

class MyTask implements TaskInterface {

    /**
     * @return array<\IdeHelper\Generator\Directive\BaseDirective>
     */
    public function collect(): array {
        // ...
    }

}
```

Then add it to the config:

```php
'IdeHelper' => [
    'generatorTasks' => [
        'MyTask' => \App\Generator\Task\MyTask::class,
    ],
],
```

The key `'MyTask'` can be any string, but it must be unique across all
existing tasks.

## Replacing Native Tasks

Using associative arrays you can swap any native task with your own
implementation:

```php
'IdeHelper' => [
    'generatorTasks' => [
        \IdeHelper\Generator\Task\ModelTask::class => \App\Generator\Task\MyEnhancedModelTask::class,
    ],
],
```

The native class name is the key, your replacement the value. Setting the
value to `null` disables a native task entirely.

## Available Directives

### Override

Most directives used by the built-in tasks are `Override`. They are also the
ones supported the longest. For a specific string method argument, an
`Override` returns a specific object — that covers a lot of CakePHP's
internal magic.

```php
$method = '\Namespace\PackageName\MyFactory::create(0)';
$map = [
    'alpha' => '\My\Cool\Alpha::class',
    'beta' => '\My\Cool\Beta::class',
];
$directive = new Override($method, $map);
```

You can also use the `ClassName` value object together with real `::class`
usage and imports:

```php
use IdeHelper\ValueObject\ClassName;
use My\Cool\Alpha;
use My\Cool\Beta;

$map = [
    'alpha' => ClassName::create(Alpha::class),
    'beta' => ClassName::create(Beta::class),
];
```

### ExpectedArguments

With this you can set default values to choose from for method arguments.
Specify the parameter count as a 0-based value.

```php
$method = '\Namespace\PackageName\MyFactory::create()';
$position = 0;
$list = [
    '\'alpha\'',
    '\'beta\'',
];
$directive = new ExpectedArguments($method, $position, $list);
```

Note the escaped quotes around literal string values. To make it cleaner,
use the `StringName` value object — it auto-quotes on output:

```php
use IdeHelper\ValueObject\StringName;

$list = [
    StringName::create('alpha'),
    StringName::create('beta'),
];
```

### ExpectedReturnValues

You can also set expected return types for a method:

```php
$method = '\Namespace\PackageName\MyFactory::create()';
$list = [
    '\My\Cool\Alpha::class',
    '\My\Cool\Beta::class',
];
$directive = new ExpectedReturnValues($method, $list);
```

### RegisterArgumentsSet

If you reuse the same lists for both arguments and return values, you can
register a set and reuse it in the directives above.

```php
$set = 'mySet';
$list = [
    '\My\Cool\Executer::SUCCESS',
    '\My\Cool\Executer::ERROR',
];
$directive = new RegisterArgumentsSet($set, $list);
```

Now you can use it as the list value `argumentsSet('mySet')` inside the
others. For this just pass the `$directive` object itself to the list, which
then contains only this one element.

You can also use the `LiteralName` value object for constants and anything
that does not need to be output as a string:

```php
use IdeHelper\ValueObject\LiteralName;

$list = [
    LiteralName::create('\My\Cool\Executer::SUCCESS'),
    LiteralName::create('\My\Cool\Executer::ERROR'),
];
```

If you want to reuse existing argument sets from other tasks, use the
`ArgumentsSet` value object referencing them:

```php
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\ValueObject\ArgumentsSet;

$method = '\\' . static::CLASS_FORMAT_HELPER . '::sidebarLink()';
$list = [
    ArgumentsSet::create(FormatIconFontAwesome5Task::SET_ICONS_FONTAWESOME),
];
$directive = new ExpectedArguments($method, 1, $list);
```

Just make sure those argument sets are actually available — this is not
checked for you.

### ExitPoint

This directive lets the IDE know what methods abort the current code flow.
The IDE shows an "Unreachable statement" warning and usually highlights the
following code in yellow to inform you.

```php
$directive = new ExitPoint('\My\Class::method()');
```

## Worked Example

Imagine you have the following methods you want to annotate:

```php
$alpha = MyFactory::create('alpha'); // Returns \My\Cool\Alpha class
$beta = MyFactory::create('beta'); // Returns \My\Cool\Beta class
```

Create an `Override` to get the correct class instance returned:

```php
$method = '\Namespace\PackageName\MyFactory::create(0)';
$map = [
    'alpha' => '\My\Cool\Alpha::class',
    'beta' => '\My\Cool\Beta::class',
];
$override = new Override($method, $map);
```

Note that map keys are usually always strings and output auto-quoted by
default. So you can treat them as simple/literal strings.

Now imagine you have multiple class methods that return the same set of
constants. First create the reusable set:

```php
$list = [
    '\My\Cool\Executer::SUCCESS',
    '\My\Cool\Executer::ERROR',
];
$argumentsSet = new RegisterArgumentsSet('mySet', $list);
```

Now you can use it for all methods:

```php
$method = '\My\Cool\Executer::execute()';
$list = [
    $argumentsSet,
];
$expectedReturnValues = new ExpectedReturnValues($method, $list);
```

Make sure your task's `collect()` method returns all of them:

```php
return [
    $override->key() => $override,
    $argumentsSet->key() => $argumentsSet,
    $expectedReturnValues->key() => $expectedReturnValues,
    // ...
];
```

As the key for directive values, always use their `->key()` string.

For more examples and details, see the [PhpStorm Advanced Metadata documentation](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata).

## Literal Keys

If you really need literal string keys (no auto-quoting), use the `KeyValue`
value object:

```php
$key = ClassName::create(Bar::class);
$value = ClassName::create(Bar::class);
$keyValue = KeyValue::create($key, $value);

// Now use it as any other value
$map = [
    'thisKeyIsOnlyForSortingNow' => $keyValue,
    // ...
];
$directive = new Override('\\' . Table::class . '::returnMy(0)', $map);
```

It allows you to control the quoting of both key and value. The map key here
is only used for sorting.

::: info Override-only
This value object can only be used for the `Override` directive — that's the
one that actually makes use of associative keys.
:::
