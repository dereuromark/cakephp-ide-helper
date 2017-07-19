#  CakePHP IdeHelper Plugin Documentation

Note that freshly [baking](https://github.com/cakephp/bake) your code will result in similar results,
but often times there is already existing code, and re-baking it is not an option then usually.
And of course it also keeps your manually addd or modified code annotated.


## Controllers
All controllers should at least annotate their primary model.
They should also annotate the other loaded models as well as the loaded components.

```
bin/cake annotations controllers
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

	public $modelClass = 'MyApples';

}
```

### Custom Prefixes
By default, the annotator supports any prefix for your controllers (as subfolder).
Using Configure key `'IdeHelper.prefixes'` you can configure a prefix whitelist.

## Models
This will ensure the annotations for tables and their entities:

```
bin/cake annotations models
```

### Tables
Tables should annotate their entity related methods, their relations and behavior mixins.

A LocationsTable class would then get the following doc block annotations added if not already present:
```php
/**
 * @method \App\Model\Entity\Location get($primaryKey, $options = [])
 * @method \App\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Location|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
 *
 * @property \App\Model\Table\ImagesTable|\Cake\ORM\Association\HasMany $Images
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
```

### Entities
Entities should annotate their fields and relations.

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
 *
 * @property \App\Model\Entity\Image[] $images
 * @property \App\Model\Entity\User $user
 */
class Location extends Entity {
}
```

## Shells
Shells and Tasks should annotate their primary model as well as all manually loaded models.

```
bin/cake annotations shells
```

```php
	/**
	 * @var string
	 */
	public $modelClass = 'Cars';

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

## View
The AppView class should annotate the helpers of the plugins and the app.

```
bin/cake annotations view
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

## Components
Components should annotate any component they use.

```
bin/cake annotations components
```

A component containing
```php
	/**
	 * @var array
	 */
	public $helpers = [
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
bin/cake annotations helpers
```

A helper containing
```php
	/**
	 * @var array
	 */
	public $helpers = [
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

## Templates
This will ensure annotations for view templates and elements:
```
bin/cake annotations templates
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
 */
?>
```

### Skipping folders
Certain template folders, like for Bake template generation, should be skipped. 
This is done by default for `/src/Template/Bake/` in your app or your plugin.

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


## Running all commands
```
bin/cake annotations all
```
By default it will be interactive, asking you for each class type if you want to continue.
You can use `-i` (interactive) to enable interactive mode. It is also recommended to make the output more verbose:
```
bin/cake annotations all -i -v
```

Also make sure you commited or backuped all project files.

## Dry-Run and Diff
If you want to check if it would be modifying any files, you can run it with `-d` (dry-run) param.

It will output a nice little diff for each modification:
```
Template/Tickets
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

## Continuous integration support
The tool can also be run like the coding standards check in your CI. 
This way no annotation can be forgotten, when making PRs for your project.

For this, use the `--ci` option along with `-d` (dry run):
```
bin/cake annotations all -v -d --ci
```
It will return an error code if any modification has to be done.

It is advised to hook it in along with your cs check, e.g. for travis:
```
- if [[ $PHPCS == 1 ]]; then bin/cake annotations all -v -d --ci ; fi
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
	}
}
```
Then read a folder, iterate over it and invoke your annotator from the shell command with a specific path.

## Configure options
You have a full list of possible Configure options, please see the `app.dist.php` file in `/config/` directory.
The content can be directly copy-pasted into your project config.
