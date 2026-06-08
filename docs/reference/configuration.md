# Configuration Reference

All Configure options live under the `IdeHelper` key in `app.php`. The
canonical reference with defaults is the `app.example.php` file in the
plugin's `/config/` directory — that file is the source of truth and can be
copy-pasted into your project config.

## Common Keys at a Glance

The list below highlights the keys mentioned across this documentation. For
the full set, defaults, and the most up-to-date options, see
[`config/app.example.php`](https://github.com/dereuromark/cakephp-ide-helper/blob/master/config/app.example.php).

### Annotator

| Key | Type | Notes |
|-----|------|-------|
| `arrayAsGenerics` | `bool` | Use modern `array<type>` instead of legacy `type[]`. |
| `genericsInParam` | `false \| true \| 'detailed'` | Tri-state for table method param types. See [Models](/annotations/models). |
| `tableEntityQuery` | `bool` | Expose entity-aware `find()` return type on tables. |
| `prefixes` | `array` | Whitelist of controller subfolder prefixes. |
| `typeMap` | `array` | Map DB types → PHP types for entity property annotations. |
| `nullableMap` | `array` | Map DB types → nullability flag. |
| `includedPlugins` | `array \| true` | Plugins to include when annotating helpers in `AppView`. |
| `templateExtensions` | `array` | File extensions processed by the templates annotator. Defaults to `['ctp', 'php']`. |
| `skipTemplatePaths` | `array` | Template folders the annotator should skip. `/templates/Bake/` is skipped by default. |
| `templateCollectionObject` | `string \| false` | FQCN (or `iterable`/`false`) used for template collection annotations. |
| `autoCollect` | `bool \| callable` | Auto-collect template variables. |
| `autoCollectBlacklist` | `array` | Strings or regex patterns of variables to exclude from auto-collection. |
| `preemptive` | `bool` | Preemptive annotations (e.g. always add `@var \App\View\AppView $this` to templates). |
| `viewClass` | `string` | Custom AppView FQCN. |
| `preferLinkOverUsesInTests` | `bool` | Use `@link` (default) vs. `@uses` in test class annotations. |
| `propertyTypeMap` | `array` | Map declared property names → inline `@var` types for class annotations. |
| `annotators` | `array` | Replace or disable native annotators. |
| `classAnnotatorTasks` | `array` | Register or replace class-annotator tasks. |
| `CallbackAnnotatorTasks` | `array` | Register or replace callback-annotator tasks. |

## Recommended Static Analysis Defaults

For projects that use PHPStan or Psalm, prefer detailed generic annotations so
generated docblocks do not leave bare `array` types behind:

```php
'IdeHelper' => [
    'arrayAsGenerics' => true,
    'objectAsGenerics' => true,
    'genericsInParam' => 'detailed',
    'tableBehaviors' => true,
    'propertyTypeMap' => [
        'actsAs' => 'array<string, mixed>',
        'helpers' => 'array<int|string, string|array<string, mixed>>',
        'components' => 'array<int|string, string|array<string, mixed>>',
        'paginate' => 'array<string, mixed>',
    ],
],
```

The `actsAs` property is commonly used by legacy CakePHP apps and plugins to
declare behaviors. Helper and component declarations can use shorthand strings
or named config arrays before CakePHP normalizes them. Controller `$paginate`
settings are associative configuration arrays. Mapping these properties through
`propertyTypeMap` lets `bin/cake annotate classes` add the missing iterable
value type without changing the native property type.

### Generator

| Key | Type | Notes |
|-----|------|-------|
| `plugins` | `array` | Include not-loaded plugins or exclude loaded ones (`-` prefix). |
| `generatorTasks` | `array` | Register or replace generator tasks. |
| `skipDatabaseTables` | `array` | Regex blacklist for the Migrations-tables generator task. |

### Code Completion

| Key | Type | Notes |
|-----|------|-------|
| `codeCompletionPath` | `string` | Custom output path for code completion files (e.g. `ROOT . DS . '.phpstorm.meta.php' . DS`). |
| `codeCompletionTasks` | `array` | Register or replace code completion tasks. |

### Illuminator

| Key | Type | Notes |
|-----|------|-------|
| `illuminatorIndentation` | `string` | Indentation whitespace; defaults to `"\t"`. Use `'    '` for spaces. |
| `IlluminatorTasks` | `array` | Register or replace Illuminator tasks. |

## Replacing or Disabling Native Tasks

For each task type, the registration array uses `'CustomKey' => ClassName`
to add a task, or the native class name as the key to replace one:

```php
'IdeHelper' => [
    'annotators' => [
        // Replace a native annotator
        \IdeHelper\Annotator\EntityAnnotator::class => \App\Annotator\MyEnhancedEntityAnnotator::class,
        // Disable a native annotator
        \IdeHelper\Annotator\HelperAnnotator::class => null,
        // Add a custom annotator
        'MyCustomAnnotator' => \App\Annotator\MyCustomAnnotator::class,
    ],
],
```

The same pattern applies to `classAnnotatorTasks`, `CallbackAnnotatorTasks`,
`generatorTasks`, `codeCompletionTasks`, and `IlluminatorTasks`.
