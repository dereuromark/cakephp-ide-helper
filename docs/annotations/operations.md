# Operations

Cross-cutting flags and workflows that apply to every annotator subcommand.

## Running All Commands

```bash
bin/cake annotate all
```

By default it runs interactively, asking you for each class type whether to
continue. You can use `-i` (interactive) to enable interactive mode. It is
also recommended to make the output more verbose:

```bash
bin/cake annotate all -i -v
```

::: warning Backup first
Make sure you have committed or backed up all project files before running
the annotator across the whole project.
:::

## Dry-Run and Diff

If you want to check whether the annotator would modify any files, run it
with `-d` (dry-run):

```bash
bin/cake annotate all -d
```

It will output a small diff for each modification:

```
templates/Tickets
-> view
   | +<?php
   | +/**
   | + * @var \App\View\AppView $this
   | + * @var \App\Model\Entity\Ticket $ticket
   | + */
   | +?>
   |  <nav class="large-3 medium-4 columns" id="actions-sidebar">
```

::: tip Verbose dry-runs
Use `-v` together with `-d` to get more information on which files were
processed.
:::

## Quick-Filter Files

With `-f` / `--filter` you can quickly annotate only specific files. The
filter is applied to the file name. For templates it also looks at the folder
name.

## Specific Files Only

With `--file` and a comma-separated list of ROOT-relative or absolute paths,
you can limit the run to those specific files. This is useful for programmatic
tooling-based runs or file watchers.

If `--file` is set, it takes precedence over `--filter` (which is ignored).

Example:

```bash
bin/cake annotate all --file src/View/AppView.php,plugins/My/src/View/Helper/MyHelper.php
```

## Removing Outdated Annotations

With `-r` / `--remove` there is basic support for finding and removing
outdated annotations.

::: warning Alpha-quality feature
Only use this after running the normal annotation flow and committing the
result, so you can review and verify the changes. This feature is still in a
very alpha phase.
:::

You can prevent removal (just like updating) by adding a comment to your
annotation. That will skip any attempt to remove it:

```php
    @property array|null $data !
```

or:

```php
    @property array|null $data ! A manual field for testing only
```

## Skipping Annotations for a Class

Sometimes you are extending another class. In that case you can use the
`@inherit` tag in the class doc block to skip annotating:

```php
/**
 * @inheritdoc
 */
class CustomImagesTable extends ImagesTable ...
```

In this case `CustomImagesTable` extends `ImagesTable` but uses the same
`protected $_entityClass = Image::class;`, so we skip annotating.

## File Watcher Setup

You can set up a file watcher to run the annotation tool on every file
change, giving you live annotation updates while coding.

### Using Node and Chokidar

Install in your project root:

```bash
npm init -y
npm install chokidar --save
```

Run:

```bash
node vendor/dereuromark/cakephp-ide-helper/annotate-watcher.cjs
```

If necessary, you can customize the paths via `--path=src/,templates/`, for
example. You can also copy `annotate-watcher.cjs` to your app and customize it
there.

Since this is cross-platform, this is the recommended approach. It is also
the most performant — only files directly modified are touched. The trade-off
is that it might miss a few related templates that are not modified but would
get updates.

### Using watchexec

See [github.com/watchexec/watchexec](https://github.com/watchexec/watchexec).

```bash
watchexec -e php 'bin/cake annotate all'
```

Here you would run the annotator over all files. Still usually quite
performant.

## Continuous Integration

The tool can be run like the coding standards check in your CI. This way no
annotation can be forgotten when making PRs.

Use the `--ci` option along with `-d` (dry run):

```bash
bin/cake annotate all -d --ci
```

It will return error code `2` if any modification has to be done.

::: info Database setup
This needs some additional setup, like running migrations prior to the call.
The database must exist and replicate the actual DB.
:::

You can also add this into a pre-commit hook for local development. Your VCS
will then refuse to commit until annotations are all in line.

## Writing Your Own Annotators

Extend `IdeHelper\Command\AnnotateCommand` at the application level, register
your command, and create your own `Annotator` class:

```php
class MyAnnotator extends AbstractAnnotator {

    /**
     * @param string $path
     * @return bool
     */
    public function annotate(string $path): bool {
        // ...
    }
}
```

Then read a folder, iterate over it, and invoke your annotator from the
command with a specific path.

## Configure Options

For the full list of possible Configure options, see the `app.example.php`
file in the plugin's `/config/` directory. The content can be directly copied
into your project config.
