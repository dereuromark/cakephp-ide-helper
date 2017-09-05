#  Meta File Generator

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

### Adding your own tasks
Just create your own Task class:
```php
<?php
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
		App\Generator\Task\MyTask::class,
	],
],
```

### CI or pre-commit check
Using `-d` (dry run) option you will get an error code if the file would need updating.
This way you can automate the check for CI tooling or commit hooks.
