# Available Tasks

The list of built-in tasks shipped with the meta file generator.

## Plugins

In your `Application.php` you can — after composer-requiring (and refreshing
the meta file) — auto-complete the available plugins for your `addPlugin()`
calls:

```php
    public function bootstrap(): void {
        // ...
        $this->addPlugin('TypeHere');
    }
```

This is especially useful for more complex and possibly vendor-prefixed
names (e.g. `'Cake/TwigView'`, note the forward slash).

## Models

```php
/** @var \App\Model\Table\UsersTable $users */
$users = TableRegistry::getTableLocator()->get('Users');
$users->doSomething();
```

So far `$users` required the annotation above to be type-hinted and
clickable. With the generated meta file this is no longer necessary. The
static factory call is detected and `$users` is hinted as
`\App\Model\Table\UsersTable`, making `doSomething()` available in the IDE
for method argument checking and following.

This task also annotates dynamic model factory calls (e.g.
`$this->getTableLocator()->get('Users')`) and `loadModel()` usage.

![Model Autocomplete loadModel](/img/model_autocomplete_loadmodel.png)

If you prefer FQCN as the argument, you still get the benefit for the return
type:

```php
use App\Model\Table\UsersTable;

$users = TableRegistry::getTableLocator()->get(UsersTable::class);
$users->doSomething();
```

It now knows the concrete object of `$users` and can autocomplete the method
call right away. You will not be able to quickly select from a list of input
options in this form, however.

## Entities

The following are auto-completed, for example:

```php
$user->setDirty('field_name');
$user->setError('field_name');
$user->getOriginal('field_name');
// ...
```

## TableAssociations

The following are auto-completed, for example:

```php
$this->belongsTo('Authors');
$this->hasOne('Book');
$this->hasMany('Articles');
$this->belongsToMany('Tags.Tags');
```

## TableFinders

The `'threaded'` string is now auto-completed, for example:

```php
$this->Posts->find('threaded');
```

::: tip Preemptive finders
Using Configure key `'IdeHelper.preemptive'` set to `true`, you can be a bit
more verbose and include all possible custom finders, including those from
behaviors.
:::

![Model Autocomplete finder](/img/model_autocomplete_finder.png)

## Behaviors

The following are auto-completed, for example:

```php
$this->addBehavior('Tools.Slugged');
$this->removeBehavior('Slugged'); // Note the alias without plugin prefix
```

## Components

The following are auto-completed, for example:

```php
$this->loadComponent('My.Useful');
$this->components()->unload('Useful'); // Note the alias without plugin prefix
```

## Helpers

The following are auto-completed, for example:

```php
$this->loadHelper('Tools.Tree');
```

And so is `addHelper()` (added in CakePHP 4.1) on the `ViewBuilder`:

```php
$this->viewBuilder()
    ->addHelper('TinyAuth.AuthUser')
    ->addHelper('Tools.Tree');
```

## Mailers

The following is auto-completed and returns the corresponding Mailer class:

```php
$userMailer = $this->getMailer('User');
```

## Types

In your bootstrap (app or plugin) you might add additional database `Type`
classes, or reconfigure existing ones:

```php
Type::build('date')->useLocaleParser()->setLocaleFormat('d.m.Y');
Type::build('datetime')->useLocaleParser()->setLocaleFormat('d.m.Y H:i');
```

The IDE recognizes the returned type of class and allows auto-complete here,
too. Same for `Type::map()` and type strings like `integer`, `string`, etc.:

```php
Type::map('decimal', ...);
```

## Elements

Heavy users of elements in templates: tired of typing the full template name
in `$this->element('...')` calls? PhpStorm auto-completes this, including all
elements from plugins.

## Layouts

`$this->viewBuilder->setLayout(...)` is auto-completed.

## Cache

`Cache::write()`, `Cache::read()` and other methods are auto-completed for
the cache engine(s) available.

## FormHelper

`$this->Form->control()` is auto-completed for the model fields available.

## Validation

### `Validator::requirePresence()`

![Validation Autocomplete Validator::requirePresence()](/img/validation_autocomplete_validator_require_presence.png)

Now not just `bool`, but also the possible "magic strings" are type-hinted
and usable as single click/enter.

## Request params

`$this->request->getParam()` auto-completes for `prefix`, `controller` and
other common keys.

## Configure keys

![Configure Autocomplete](/img/configure_autocomplete.png)

`Configure::read()` and the other methods are auto-completed for currently
existing keys. Numeric keys are excluded as they are usually not part of an
associative array config.

## ENV keys

`env()` is auto-completed for most common and used keys.

## Translation keys

Using `__()` and `__d()` can be auto-completed based on your project's `.po`
files.

::: info Quoting limitation
PhpStorm is [not smart enough yet](https://youtrack.jetbrains.com/issue/WI-52508)
to auto-adjust any (escaped or not) quotes in your strings. So in those cases
you must use `'` as delimiters for your strings if you want auto-complete:

```php
<?php echo __('A "quoted" string'); ?>
<?php echo __('A \'literally quoted\' string'); ?>
<?php echo __('A variable \'\'{0}\'\' be replaced.', __('will')); ?>
```

Any further `'` inside will be escaped for you.
:::

## ConnectionManager

`ConnectionManager::get()` is auto-completed for the currently configured
connection aliases.

## Fixtures

`TestCase::addFixture()` is auto-completed for the currently available
fixtures from app, core, and plugins.

## Migrations plugin database tables

When using the Migrations plugin, this task comes in handy to quickly
autocomplete existing tables, their column names, and possible column types.

It excludes CakePHP internal tables and all `phinxlog` ones by default. You
can use a regex blacklist to further exclude certain tables:

```php
'IdeHelper' => [
    'skipDatabaseTables' => [
        '/customRegexPattern/',
        // ...
    ],
],
```
