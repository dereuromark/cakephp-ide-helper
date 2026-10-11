# Getting started

Follow [Installation](./installation) first. Run the commands below from your
application directory, with your application's normal configuration available.
Commit or back up your source files before applying annotations.

## Preview controller annotations

```bash
bin/cake annotate controllers -d -v
```

`-d` shows the changes without writing source files. `-v` identifies the files
being processed. Review the diff, then apply it:

```bash
bin/cake annotate controllers
```

Review `git diff` before committing the generated doc blocks. They describe
properties such as tables and components so your IDE can resolve their types.
If the preview reports a missing table or database connection, check your
application configuration and run its migrations before continuing. Annotating
models needs access to the application's database schema.

## Add IDE completion files

```bash
bin/cake generate code_completion
```

This writes `CodeCompletion*.php` stubs into the application's `tmp/` directory.
Allow your IDE to index those files. Keep them out of application autoloading:
they describe existing framework classes for the editor and are not runtime
implementations. Regenerate them after changing behaviors or upgrading CakePHP.

For PhpStorm or VS Code with Intelephense, also run:

```bash
bin/cake generate phpstorm
```

This writes `.phpstorm.meta.php/.ide-helper.meta.php` at the application root.
The [generator guide](/generator/) explains the available metadata and tasks.

## Expand the annotation run

Once the first preview looks correct, preview the other supported class types:

```bash
bin/cake annotate all -d -v
```

The command runs without prompts by default. Add `-i` to choose each class type
interactively. After reviewing the preview, omit `-d` to apply the changes.

For a CI check that fails when annotations need updating, use:

```bash
bin/cake annotate all -d --ci
```

The CI command exits with code `2` when it finds changes. An ordinary dry run
without `--ci` succeeds even when it reports changes.

See [Usage](./usage) for plugin-specific runs and [Operations](/annotations/operations)
for filters and file watchers. [Illuminator](/illuminator/) tasks rewrite PHP
code and have a separate preview workflow; they are optional for this setup.
