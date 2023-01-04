# Annotations Updater

Note that freshly [baking](https://github.com/cakephp/bake) your code will result in similar results,
but often times there is already existing code, and re-baking it is not an option then usually.
And of course it also keeps your manually added or modified code annotated.

## Important options to start with
The following will be defined under `IdeHelper` key of `app.php` config:
- `arrayAsGenerics`: Set to true to have modern generics syntax used (`array<type>` instead of legacy `type[]`).

## Controllers
All controllers should at least annotate their primary model.
They should also annotate the other loaded models as well as the loaded components.

```
bin/cake annotate controllers
```

### Primary model via convention
```php
<?php
namespace App\Controller;

class ApplesController extends AppController {
}
```
becomes
```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\ApplesTable $Apples
 */
class ApplesController extends AppController {
}
```
You get autocompletion on any `$this->Apples->...()` usage in your controllers then.

Use `-p PluginName` to annotate inside a plugin. It will then use the plugin name as namespace.

### Primary model via $modelClass definition
When defining `$modelClass` it will be used instead:
```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\MyApplesTable $MyApples
 */
class ApplesController extends AppController {

    protected $modelClass = 'MyApples';

}
```

### Custom Prefixes
By default, the annotator supports any prefix for your controllers (as subfolder).
Using Configure key `'IdeHelper.prefixes'` you can configure a prefix whitelist.

## Models
This will ensure the annotations for tables and their entities:

```
bin/cake annotate models
```

### Tables
Tables should annotate their entity related methods, their relations and behavior mixins.

A LocationsTable class would then get the following doc block annotations added if not already present:
```php
/**
 * @method \App\Model\Entity\Location get($primaryKey, $options = [])
 * @method \App\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Location|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Location saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
 *
 * @property \App\Model\Table\ImagesTable&\Cake\ORM\Association\HasMany $Images
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
```

### Entities
Entities should annotate their properties and relations.

A Location entity could look like this afterwards:
```php
/**
 * @property int $id
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $location
 * @property string $details
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property string|null $virtual_property
 *
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\User $user
 */
class Location extends Entity {
}
```

Using Configure key `'IdeHelper.typeMap'` you can set a custom array of types to be used for the field mapping here.
Overwriting the defaults of this plugin is also possible, to skip (reset) just set the value to null:
```php
    'IdeHelper' => [
        'typeMap' => [
            'custom' => 'array',
            'longtext => null,
            ...
         ],
    ],
```

Using Configure key `'IdeHelper.nullableMap'` you can set a custom array of types and if they can be nullable:
```php
    'IdeHelper' => [
        'nullableMap' => [
            'custom' => false,
            'longtext => true,
            ...
         ],
    ],
```

For virtual properties it looks up the respective `_get...()` methods (e.g. `_getVirtualProperty()` for `$virtual_property`).
It first checks the documented type in the doc block's `@return`, otherwise (given PHP 7.0+) tries to read it from the
return type hint (e.g. `: ?string`). Only if that is also not present it will use the fallback type `mixed`.

Note: You can also use `@property-read` tag here if it is a pure virtual field getter.

## Shells
Shells and Tasks should annotate their primary model as well as all manually loaded models.

```
bin/cake annotate shells
```

```php
    /**
     * @var string
     */
    protected $modelClass = 'Cars';

    /**
     * @return void
     */
    public function main() {
        $this->loadModel('MyPlugin.Wheels');
    }
```
will result in the following annotation:

```php
/**
 * @property \MyPlugin\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 */
```

They also should annotate any Tasks they use.

## View
The AppView class should annotate the helpers of the plugins and the app.

```
bin/cake annotate view
```

With template content like
```html
<?php echo $this->My->foo($bar); ?>
<?php if ($this->Configure->baz()) {} ?>
```
the following would be annotated (if `My` and `Shim.Configure` helpers were loaded correctly):
```php
/**
 * @property \App\View\Helper\MyHelper $My
 * @property \Shim\View\Helper\ConfigureHelper $Configure
 */
class AppView extends View {
}
```

### Include plugins
Using Configure key `'IdeHelper.includedPlugins'` you can set an array of (loaded!) plugins to include.
Those will then also be parsed and all found helpers added to the AppView annotations. Setting this to `true` will auto-include all loaded plugins.

## Components
Components should annotate any component they use.

```
bin/cake annotate components
```

A component containing
```php
    /**
     * @var array
     */
    protected $components = [
        'RequestHandler',
        'Flash.Flash',
    ];
```
would get the following annotations:
```php
/**
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Flash\Controller\Component\FlashComponent $Flash
 */
```

## Helpers
Helpers should annotate any helper they use.

```
bin/cake annotate helpers
```

A helper containing
```php
    /**
     * @var array
     */
    protected $helpers = [
        'Form',
    ];

    /**
     * @param \Cake\View\View $View
     * @param array $config
     */
    public function __construct(View $View, array $config = []) {
        parent::__construct($View, $config);
        $this->_View->loadHelper('Template');
    }
```
would get the following annotations:
```php
/**
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \App\View\Helper\TemplateHelper $Template
 */
```

## Routes
Route files in 4.x are not needed to be static anymore.
For this we need the object to work with annotated.
`config/routes.php` files would get the following annotation:
```php
/**
 * @var \Cake\Routing\RouteBuilder $routes
 */
```

## Replacing native tasks
Using associative arrays you can even exchange any native task with your own implementation:
```php
'IdeHelper' => [
    'annotators' => [
        \IdeHelper\Annotator\EntityAnnotator::class => \App\Annotator\MyEnhancedEntityAnnotator::class,
    ],
],
```

## Classes and ClassAnnotationTasks

In order to run certain "fixers" over all classes, class annotations and their tasks are available.
Out of the box the following tasks are run:

### ModelAware

Any `use ModelAwareTrait` usage together with `$this->loadModel(...)` calls will add the required annotation on top of the class.

### Form::execute()
This will add a convenience inline annotation to quickly jump to the actual business logic code.
```php
use App\Form\ReleaseForm;

$releaseForm = new ReleaseForm();

/** @uses \App\Form\ReleaseForm::_execute() */
$releaseForm->execute($data);
```

### Mailer::send()
This will add a convenience inline annotation to quickly jump to the actual business logic code.
```php
use App\Mailer\NotificationMailer;
// or
$notificationMailer = $this->getMailer('Notification');

/** @uses \App\Mailer\NotificationMailer::notify() */
$notificationMailer->send('notify', [$user, $details]);
```

### Test

Any test class of specific types can be annotated with the corresponding class it tests.
This is mainly useful for the following types, as they are invoked indirectly via Integration test harness:
- Controller
- Command

Here the `@uses` statements added help to quick-jump to the class if needed.

### Custom Tasks

Just create your own Task class:
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
    public function shouldRun($path, $content) {
        ...
    }

    /**
     * @param string $path
     * @return bool
     */
    public function annotate($path) {
        ...
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

#### Replacing native tasks
Using associative arrays you can even exchange any native task with your own implementation:
```php
'IdeHelper' => [
    'classAnnotatorTasks' => [
        \IdeHelper\Annotator\ClassAnnotatorTask\ModelAwareTask::class => \App\Annotator\ClassAnnotatorTask\MyEnhancedModelAwareTask::class,
    ],
],
```
The native class name is the key then, your replacement the value.
Setting the value to `null` completely disables a native task.


## Templates
This will ensure annotations for view templates and elements:
```
bin/cake annotate templates
```
Templates should have a `/** @var \App\View\AppView $this */` added on top if they use any helper or access the request object.
They should also annotate entities they use.

A template such as
```html
<h2>Some header</h2>
<?php echo $this->Form->create($user); ?>
<?php foreach ($groups as $group): ?>
<?php endforeach; ?>
<li><?= $this->Html->link(__('Edit Email'), ['action' => 'edit', $email->id]) ?> </li>
```
would then get the following added on top:
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

### Extensions
To adjust the template extensions being processed set `IdeHelper.templateExtensions` via Configure.
By default, all files of type `'ctp', 'php'` will be checked.

Note: All template annotating is around PHP templates. Twig templates are not working with this.
Usually Twig templates have their own tooling - but will have some serious drawbacks on what this plugin provides:
Auto-complete/Type-hinting as well as IDE introspection of variable types.

### Skipping folders
Certain template folders, like for Bake template generation, should be skipped.
This is done by default for `/templates/Bake/` in your app or your plugin.

If you want to adjust this, set `IdeHelper.skipTemplatePaths` via Configure:
```
    'IdeHelper' => [
        'skipTemplatePaths' => [
            ...
        ],
    ],
```

### Skipping variables
In some cases the existing annotations might be matching different entities (e.g. Plugin vs App namespace).
If those would be replaced wrongly, you can easily mark them to be ignored by adding any comment description to it:
```php
<?php
/**
 * @var \App\View\AppView $this
 * @var \My\Custom\Entity $car !
 */
?>
```
The `!` would prevent the entity annotation to be replaced.

### Auto collecting variables
The IdeHelper can by default auto collect template variables and add them to the list above.
Set `'IdeHelper.autoCollect'` to false to disable this.
It defaults to `'mixed'` where the type cannot be guessed/detected.

If you need more control over it, you can configure a callable to detect/guess:
```php
'IdeHelper.autoCollect', function(array $variable) {
    if ($variable['name'] === 'date') {
        return 'Cake\I18n\FrozenTime';
    }
    return null;
});
```

Tip: In order for the best experience of auto-collecting make sure to have unique variables inside the template(s).
If you pass down a `$user` variable from the controller, make sure you are not overwriting them in some local scope.
```php
// This will skip the other $user annotation
foreach ($role->users as $user) {}

// Use a better name instead to keep $user annotation
foreach ($role->users as $rolUser) {}
```

You can use `'IdeHelper.autoCollectBlacklist'` config to exclude certain variables.
The array accepts both strings or regexp patterns like `'/^\_.+$/i'` for underscore prefixed variables).

### Entity collections
Usually, all collections (pagination, find) are object collections when being passed to the view layer.
As such, the template annotates added for it are e.g.
```
// Using objectAsGenerics false
@var \App\Model\Entity\Article[]|\Cake\Collection\CollectionInterface $articles

// Using objectAsGenerics true
@var \Cake\Collection\CollectionInterface<\App\Model\Entity\Article> $articles
```

The config `IdeHelper.templateCollectionObject` can be set to a FQCN string if you want to display a custom class (e.g. `\Cake\Datasource\ResultSetInterface`).
You can also set it to `iterable` (recommended) if you don't use any of the specific interface methods (just iterating over them):
```
// Using templateCollectionObject set to 'iterable'
@var iterable<\App\Model\Entity\Article> $articles
```

If you always pass them an array, you can use `IdeHelper.templateCollectionObject` set to `false` to reflect this in the annotations:
```
// Using arrayAsGenerics false
@var \App\Model\Entity\Article[] $articles

// Using arrayAsGenerics true
@var array<\App\Model\Entity\Article> $articles
```

### Preemptive annotating
Using Configure key `'IdeHelper.preemptive'` set to `true` you can be a bit more preemptive in annotations.
E.g. `@var \App\View\AppView $this` will then be always added to View templates, even if not currently needed.
This allows to have immediate typehinting once actually needed, it is recommended to enable this setting.

### Custom View class
Using Configure key `'IdeHelper.viewClass'` a custom class name can be set to use instead of the default.
E.g. `'App\View\MyCustomAppView'` or `MyCustomAppView::class` (incl. use statement).

## Running all commands
```
bin/cake annotate all
```
By default it will be interactive, asking you for each class type if you want to continue.
You can use `-i` (interactive) to enable interactive mode. It is also recommended to make the output more verbose:
```
bin/cake annotate all -i -v
```

Also make sure you committed or backuped all project files.


## Callbacks and CallbackAnnotationTasks

This is a separate annotations tool that focuses on methods and their doc block instead of classes.
By default it ships with
- TableCallbackAnnotatorTask

### Table callback annotations
Behaviors and generic code use the following signature:
```php
/**
 * @param \Cake\Event\EventInterface $event Event
 * @param \Cake\Datasource\EntityInterface $entity Entity
 * @param \ArrayObject $options Options
 * @return void
 */
public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
```
And as long you only use methods and attributes of the EntityInterface (as contracted here), this is fine.

But in specific Table class code, you usually also access the entities' concrete properties.
Here using this typehint is somewhat a lie. To please IDE and tooling like PHPStan we can at least fix up the doc block, however.
And that is what this task is doing, declaring the Post entity to be available and used inside.

Inside the concrete PostsTable after running the `callbacks` command:
```php
/**
 * @param \Cake\Event\EventInterface $event Event
 * @param \App\Model\Entity\Post $entity Entity
 * @param \ArrayObject $options Options
 * @return void
 */
public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
```

### Entity virtual field setter/getter annotations
A virtual field will be "linked" to the property it handles:
```php
/**
 * @see \App\Model\Entity\MyEntity::$expected_type
 *
 * @return int|null
 */
protected function _getExpectedType(): ?int
```
This way you can quick-jump there from the property and vice versa within your IDE.

### Custom Tasks

Just create your own Task class:
```php
namespace App\Annotator\CallbackAnnotatorTask;

use IdeHelper\Annotator\CallbackAnnotatorTask\AbstractCallbackAnnotatorTask;
use IdeHelper\Annotator\CallbackAnnotatorTask\CallbackAnnotatorTaskInterface;

class MyCallbackAnnotatorTask extends AbstractCallbackAnnotatorTask implements CallbackAnnotatorTaskInterface {

    /**
     * @param string $path
     * @return bool
     */
    public function shouldRun($path) {
        ...
    }

    /**
     * @param string $path
     * @return bool
     */
    public function annotate($path) {
        ...
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

Overwriting the existing tasks works the same way as above for classes.


## Dry-Run and Diff
If you want to check if it would be modifying any files, you can run it with `-d` (dry-run) param.

It will output a nice little diff for each modification:
```
templates/Tickets
-> view
   | +<?php
   | +/**
   | + * @var \App\View\AppView $this
   | + * @var \App\Model\Entity\Ticket $ticket
   | + */
   | +?>
   |  <nav class="large-3 medium-4 columns" id="actions-sidebar">
```

Tip: Use it together with `-v` (verbose) to get more information on what files got processed.

## Quick-Filter files
With the `-f`/`--filter` option you can quickly annotate only specific files. It will apply the filter to the file name.
For templates it will also look in the folder name.

## Removing outdated annotations
NEW: With `-r`/`--remove` there is now basic support for finding and removing outdated annotations.
Please use this only after normally running and committing your annotation changes to be sure you can review and
verify the changes. This feature is still in a very alpha phase.

Note that you can prevent removal (just as updating) by adding a comment to your annotation.
That will skip any attempt to remove it.
```php
    @property array|null $data !
```
or
```php
    @property array|null $data ! A manual field for testing only
```
for example.

## Skipping annotations for a class
Sometimes you are extending another class, in that case you can use `@inherit` tag as class doc block to skip annotating here.

In this case this table extend the Images table, but use the same `protected $_entityClass = Image::class;`, so skipping:
```php
/**
 * @inheritdoc
 */
class CustomImagesTable extends ImagesTable ...
```

## Continuous integration support
The tool can also be run like the coding standards check in your CI.
This way no annotation can be forgotten, when making PRs for your project.

For this, use the `--ci` option along with `-d` (dry run):
```
bin/cake annotate all -v -d --ci
```
It will return an error code 2 if any modification has to be done.

It is advised to hook it in along with your cs check, e.g. for travis:
```
- if [[ $PHPCS == 1 ]]; then bin/cake annotate all -v -d --ci ; fi
```
Note: This will need some additional setup, like migrations to be run prior to the call.
The database must exist and replicate the actual DB.

You can definitely add this into a pre-commit hook, though, for local development.
This way your VCS would not commit before those annotations are all in line.

## Writing your own annotators
Just extend the shell on application level, add your command and create your own Annotator class:
```php
class MyAnnotator extends AbstractAnnotator {

    /**
     * @param string $path
     * @return bool
     */
    public function annotate($path) {
        ...
    }
}
```
Then read a folder, iterate over it and invoke your annotator from the shell command with a specific path.

## Configure options
You have a full list of possible Configure options, please see the `app.example.php` file in `/config/` directory.
The content can be directly copy-pasted into your project config.
