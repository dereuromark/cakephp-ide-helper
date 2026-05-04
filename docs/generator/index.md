# Meta File Generator

![Model Typehinting](/img/model_typehinting.png)

![Model Autocomplete](/img/model_autocomplete.png)

The meta file generator produces `.phpstorm.meta.php` files that PhpStorm and
VS Code (with PHP Intelephense) use to understand factories, magic strings,
and return types throughout the CakePHP code base.

## PhpStorm

This command generates `.ide-helper.meta.php` in your app's
`ROOT/.phpstorm.meta.php/` directory:

```bash
bin/cake generate phpstorm
```

Make sure it is indexed (a restart of PhpStorm may be required).

::: info Why a directory?
We use a directory here to allow custom and manually created meta files
alongside this generated file. Any file inside this directory will be parsed
and used. Prefixing the file with a `.` is recommended so PHPCS skips it
automatically.
:::

## What's Next

- [Available Tasks](./tasks) — every built-in task and what it covers
- [Custom Tasks and Directives](./custom-tasks) — add your own tasks, available directives, examples
- [Operations](./operations) — include/exclude plugins, CI checks, reusing argument sets
