# Controllers

All controllers should at least annotate their primary model. They should also
annotate the other loaded models as well as the loaded components.

```bash
bin/cake annotate controllers
```

## Primary Model via Convention

```php
<?php
namespace App\Controller;

class ApplesController extends AppController {
}
```

becomes:

```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\ApplesTable $Apples
 */
class ApplesController extends AppController {
}
```

You get autocompletion on any `$this->Apples->...()` usage in your controllers.

Use `-p PluginName` to annotate inside a plugin.

::: tip Plugin wildcards
Use `*` to refer to a group of plugins, e.g. `-p SomePrefix/*` for everything
under your own `plugins/` directory. You can also use `all` for all app
plugins.

For more than one plugin the command will not run into `vendor/` plugins, to
avoid accidental modification there.
:::

## Primary Model via `$defaultTable`

When defining `$defaultTable` it will be used instead:

```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\MyApplesTable $MyApples
 */
class ApplesController extends AppController {

    protected ?string $defaultTable = 'MyApples';

}
```

## Pagination

When a controller action paginates, a `paginate()` return type annotation is added
so both the IDE and PHPStan know which entity comes back:

```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\ApplesTable $Apples
 *
 * @method \Cake\Datasource\Paging\PaginatedInterface<int, \App\Model\Entity\Apple> paginate(\Cake\Datasource\RepositoryInterface|\Cake\Datasource\QueryInterface|string|null $object = null, array $settings = [])
 */
class ApplesController extends AppController {

    public function index() {
        $apples = $this->paginate();
    }

}
```

`\Cake\Datasource\Paging\PaginatedInterface` is what `Controller::paginate()` actually
returns, so the paging API (`currentPage()`, `pageCount()`, `totalCount()`, `items()`, ...)
resolves as well as the entity type when iterating.

::: warning Re-run the annotator after upgrading
Older versions annotated `\Cake\Datasource\ResultSetInterface` here, which made PHPStan
report the paging methods as undefined. Run `bin/cake annotate controllers` once to
refresh existing annotations.
:::

## Custom Prefixes

By default, the annotator supports any prefix for your controllers (as a
subfolder). Using the Configure key `'IdeHelper.prefixes'` you can configure a
prefix whitelist.
