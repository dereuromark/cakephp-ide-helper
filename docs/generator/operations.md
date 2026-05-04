# Operations

Cross-cutting flags and workflows for the meta file generator.

## Include/Exclude Plugins

Many plugins do not need to be "loaded" — those would normally not be
included in the generator tasks. If you want to add some not-loaded plugins
into the list of plugins to process, use:

```php
'IdeHelper' => [
    'plugins' => [
        'MyNotLoadedPlugin',
        '-BlacklistedLoadedPlugin',
    ],
],
```

With the `-` prefix you can even exclude loaded plugins from being processed.

## CI or Pre-Commit Check

Using `-d` (dry run) you will get error code `2` if the file would need
updating:

```bash
bin/cake generate phpstorm -d
```

This way you can automate the check for CI tooling or commit hooks.

## Reusing Argument Sets

You can reuse argument sets that are present from any of the built-in or your
custom tasks.

In verbose mode the console gives you the available sets for re-use:

```bash
bin/cake generate phpstorm -v
```

You can then directly make use of them in any matching directive (for such
lists):

- `ExpectedArguments`
- `ExpectedReturnValues`
