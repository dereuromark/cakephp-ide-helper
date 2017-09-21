#  Meta File Generator

![Model Typehinting](img/model_typehinting.png)

![Model Autocomplete](img/model_autocomplete.png) 

## Phpstorm
This command will generate your `.phpstorm.meta.php` in your app's root dir:
```
bin/cake phpstorm generate
```

Make sure it is indexed (maybe a restart of PhpStorm could be required).

### Models
```php
/** @var \App\Model\Table\UsersTable $users */
$users = TableRegistry::get('Users');
$users->doSomething($user);
```
So far `$users` required the annotation above to be typehinted and clickable.
With the generated meta file this becomes not necessary anymore.
It will automatically detect this static factory call in the map and hint `$users` as `\App\Model\Table\UsersTable`, making
`doSomething()` available in the IDE for method argument checking and following.

This task also annotates the dynamic model factory calls (e.g. `$this->getTableLocator()->get('Users')`) or `loadModel()` usage.

### Adding your own tasks
Just create your own Task class:
```php
namespace App\Generator\Task;

use IdeHelper\Generator\Task\TaskInterface;

class MyTask implements TaskInterface {

	/**
	 * @return array
	 */
	public function collect() {
		...
	}
	
}
```

Then add it to the config:
```php
'IdeHelper' => [
	'generatorTasks' => [
		\App\Generator\Task\MyTask::class,
	],
],
```

#### Replacing native tasks
Using associative arrays you can even exchange any native task with your own implementation:
```php
'IdeHelper' => [
	'generatorTasks' => [
		\IdeHelper\Generator\Task\ModelTask::class => \App\Generator\Task\MyEnhancedModelTask::class,
	],
],
```
The native class name is the key then, your replacement the value.

#### Example
So let's imagine you have the following methods you want to annotate:
```php
$alpha = MyFactory::create('alpha'); // Returns \My\Cool\Alpha class
$beta = MyFactory::create('beta'); // Returns \My\Cool\Beta class
```
Then make sure your Task's `collect()` method returns something like:
```php
[
	'\Namespace\PackageName\MyFactory::create(0)' => [
		'alpha' => '\My\Cool\Alpha::class',
		'beta' => '\My\Cool\Beta::class',
	]
]
```

For more examples and details see their [documentation](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata).

### CI or pre-commit check
Using `-d` (dry run) option you will get an error code 2 if the file would need updating.
This way you can automate the check for CI tooling or commit hooks.
