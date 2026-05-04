# Commands and Routes

## Commands

Commands should annotate their primary model as well as all manually loaded
models.

```bash
bin/cake annotate commands
```

```php
    /**
     * @var string|null
     */
    protected ?string $defaultTable = 'Cars';

    /**
     * @return int
     */
    public function execute(Arguments $args, ConsoleIo $io): int {
        $this->fetchTable('MyPlugin.Wheels');

        return static::CODE_SUCCESS;
    }
```

results in:

```php
/**
 * @property \MyPlugin\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 */
```

## Routes

Route files in 4.x are no longer required to be static. The
`config/routes.php` file gets the following annotation so the `$routes` object
is type-hinted for editors and analyzers:

```php
/**
 * @var \Cake\Routing\RouteBuilder $routes
 */
```
