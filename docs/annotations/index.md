# Annotations Updater

The Annotator keeps doc blocks and annotations in sync with your code without
modifying functional code.

Note that freshly [baking](https://github.com/cakephp/bake) your code produces
similar results, but most projects already have existing code where re-baking
is not an option. The annotator also keeps manually added or modified code
annotated.

## Important Options to Start With

The following are defined under the `IdeHelper` key in `app.php`:

- `arrayAsGenerics`: Set to `true` to use modern generics syntax (`array<type>`
  instead of legacy `type[]`).

See the [Configuration reference](/reference/configuration) for the full list.

## Per-Class-Type Reference

Each class type has its own page covering the dedicated subcommand and the
options that apply only to that type:

- [Controllers](./controllers) — `bin/cake annotate controllers`
- [Models](./models) — `bin/cake annotate models` (Tables and Entities)
- [View, Components, Helpers](./view) — `annotate view`, `annotate components`, `annotate helpers`
- [Templates](./templates) — `bin/cake annotate templates`
- [Commands and Routes](./commands) — `bin/cake annotate commands`
- [Classes](./classes) — class-level annotators (`ModelAware`, `Form::execute()`, `Mailer::send()`, `Test`, custom)
- [Callbacks](./callbacks) — method-level annotators (`TableCallbackAnnotatorTask`, custom)

## Cross-Cutting

- [Operations](./operations) — running all commands, dry-run, filters, removal, file watcher, CI
- [Custom Class Annotators](./custom-class-annotator) — full PHP-AST example

## Replacing Native Tasks

Using associative arrays you can swap any native task with your own
implementation:

```php
'IdeHelper' => [
    'annotators' => [
        \IdeHelper\Annotator\EntityAnnotator::class => \App\Annotator\MyEnhancedEntityAnnotator::class,
    ],
],
```

The native class name is the key, your replacement the value. Setting the
value to `null` disables a native task entirely.
