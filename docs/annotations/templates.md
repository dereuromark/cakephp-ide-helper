# Templates

Annotate view templates and elements:

```bash
bin/cake annotate templates
```

Templates should have a `/** @var \App\View\AppView $this */` added on top if
they use any helper or access the request object. They should also annotate
entities they use.

A template such as:

```php
<h2>Some header</h2>
<?php echo $this->Form->create($user); ?>
<?php foreach ($groups as $group): ?>
<?php endforeach; ?>
<li><?= $this->Html->link(__('Edit Email'), ['action' => 'edit', $email->id]) ?> </li>
```

would get the following added on top:

```php
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Email $email
 * @var \App\Model\Entity\Group[] $groups
 * @var \App\Model\Entity\User $user
 */
?>
```

## Extensions

To adjust which template extensions are processed, set
`IdeHelper.templateExtensions` via Configure. By default, all files of type
`'ctp'` and `'php'` will be checked.

::: info Twig templates
All template annotating is around PHP templates. Twig templates are not
supported. Twig usually has its own tooling — but it has serious drawbacks on
what this plugin provides: auto-complete and type-hinting as well as IDE
introspection of variable types.
:::

## Skipping Folders

Certain template folders, like Bake template generation, should be skipped.
This is done by default for `/templates/Bake/` in your app or your plugin.

If you want to adjust this, set `IdeHelper.skipTemplatePaths` via Configure:

```php
'IdeHelper' => [
    'skipTemplatePaths' => [
        // ...
    ],
],
```

## Skipping Variables

In some cases existing annotations might match different entities (e.g.
plugin vs. app namespace). To prevent them from being replaced incorrectly,
mark them to be ignored by adding any comment description:

```php
<?php
/**
 * @var \App\View\AppView $this
 * @var \My\Custom\Entity $car !
 */
?>
```

The `!` prevents the entity annotation from being replaced.

## Auto-Collecting Variables

The IdeHelper can auto-collect template variables and add them to the list
above. Set `'IdeHelper.autoCollect'` to `false` to disable this. It defaults
to `'mixed'` where the type cannot be guessed/detected.

If you need more control, configure a callable to detect or guess:

```php
'IdeHelper.autoCollect', function(array $variable) {
    if ($variable['name'] === 'date') {
        return 'Cake\I18n\DateTime';
    }
    return null;
});
```

::: tip Unique variables
For the best experience of auto-collecting, use unique variable names inside
the template(s). If you pass down a `$user` variable from the controller,
make sure you are not overwriting it in some local scope.

```php
// This will skip the other $user annotation
foreach ($role->users as $user) {}

// Use a better name instead to keep $user annotation
foreach ($role->users as $rolUser) {}
```
:::

You can use the `'IdeHelper.autoCollectBlacklist'` config to exclude certain
variables. The array accepts both strings and regexp patterns like
`'/^\_.+$/i'` for underscore-prefixed variables.

## Entity Collections

Usually, all collections (pagination, find) are object collections when passed
to the view layer. The template annotations added for them are e.g.:

```
// objectAsGenerics false
@var \App\Model\Entity\Article[]|\Cake\Collection\CollectionInterface $articles

// objectAsGenerics true
@var \Cake\Collection\CollectionInterface<\App\Model\Entity\Article> $articles
```

The config `IdeHelper.templateCollectionObject` can be set to a FQCN string if
you want to display a custom class (e.g. `\Cake\Datasource\ResultSetInterface`).
You can also set it to `iterable` (recommended) if you don't use any of the
specific interface methods (just iterating over them):

```
// templateCollectionObject set to 'iterable'
@var iterable<\App\Model\Entity\Article> $articles
```

If you always pass arrays, you can set `IdeHelper.templateCollectionObject` to
`false` to reflect this in the annotations:

```
// arrayAsGenerics false
@var \App\Model\Entity\Article[] $articles

// arrayAsGenerics true
@var array<\App\Model\Entity\Article> $articles
```

## Preemptive Annotating

Using Configure key `'IdeHelper.preemptive'` set to `true` you can be a bit
more preemptive in annotations. E.g. `@var \App\View\AppView $this` will then
always be added to view templates, even if not currently needed. This allows
immediate type-hinting once actually needed; it is recommended to enable this
setting.

## Custom View Class

Using Configure key `'IdeHelper.viewClass'` a custom class name can be set to
use instead of the default. E.g. `'App\View\MyCustomAppView'` or
`MyCustomAppView::class` (incl. `use` statement).
