#  Meta File Generator

![Model Typehinting](img/model_typehinting.png)

![Model Autocomplete](img/model_autocomplete.png) 

## Phpstorm
This command will generate your `.ide-helper.meta.php` in your app's `ROOT/.phpstorm.meta.php/` directory:
```
bin/cake phpstorm generate
```

Make sure it is indexed (maybe a restart of PhpStorm could be required).

Note: We are using a directory here to allow custom and manually created meta files along with this generated file.
Any file inside this directory will be parsed and used. Prefixing with a `.` dot is recommended for PHPCS to skip this file automatically.

### Available tasks

#### Plugins
In your `Application.php` you can, after composer requiring (and refreshing meta file), auto-complete the available plugins for your `addPlugin()` calls:
```php
    public function bootstrap() {
        ...
        $this->addPlugin('TypeHere');
    }
```
This is especially useful for more complex and possibly vendor-prefix names (e.g. `'WyriHaximus/TwigView'`, note the forward slash).

#### Models
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

![Model Autocomplete loadModel](img/model_autocomplete_loadmodel.png)

#### TableAssociations
The following is now auto-completed, for example:
```php
$this->belongsTo('Authors');
$this->hasOne('Book');
$this->hasMany('Articles');
$this->belongsToMany('Tags.Tags');
```

#### TableFinders
The `'threaded'` string is now auto-completed, for example:
```php
$this->Posts->find('threaded')
```

Note: Using Configure key `'IdeHelper.preemptive'` set to `true` you can be a bit more verbose and include all possible custom finders, including those from behaviors.


#### Behaviors
The following is now auto-completed, for example:
```php
$this->addBehavior('Tools.Slugged')
```

#### Components
The following is now auto-completed, for example:
```php
$this->loadComponent('Security')
```

#### Helpers
The following is now auto-completed, for example:
```php
$this->loadHelper('Tools.Tree')
```

#### Types
In your bootstrap (app, or plugin), you might add additional database Type classes, or you reconfigure existing ones:
```php
Type::build('date')->useLocaleParser()->setLocaleFormat('d.m.Y');;
Type::build('datetime')->useLocaleParser()->setLocaleFormat('d.m.Y H:i');
```
The IDE will now recognize the returned type of class and allow auto-complete here, too.

#### Elements
Are you making heavy use of elements in templates?
Tired of typing the full template name in `$this->element('...')` calls?

With this generator PHPStorm can auto-complete this, including all elements for plugins.

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
        'MyTask' => \App\Generator\Task\MyTask::class,
    ],
],
```
The key `'MyTask'` can be any string.

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
Setting the value to `null` completely disables a native task.

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
