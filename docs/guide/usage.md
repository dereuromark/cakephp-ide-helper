# Usage

A short tour of the high-level commands. Each section has a dedicated guide
with the full set of options.

## Annotations

Run on your app:

```bash
bin/cake annotate [type]
```

By default it prints a diff of the changes plus the number of modified lines.

Run on a loaded plugin:

```bash
bin/cake annotate [type] -p FooBar
```

The plugin is autoloaded if needed (when not manually loaded already).

Use `-v` for verbose output:

```bash
bin/cake annotate [type] -v
```

Add `-d` (`--dry-run`) to simulate the output without modifying files.

See [Annotations](/annotations/) for the full reference.

## Code Completion

The code completion file aims to be generic and to work with all IDEs.

Generate the code completion files into `TMP/`:

```bash
bin/cake generate code_completion
```

See [Code Completion](/code-completion/).

## Meta File Generator

The meta file is supported by:

- PhpStorm (2016.2+)
- VS Code with the [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client) plugin

Generate the app-level `.phpstorm.meta.php` file:

```bash
bin/cake generate phpstorm
```

See [Generator](/generator/).

## Illuminator

Rewrite PHP files using configured Illuminator tasks:

```bash
bin/cake illuminate code <path>
```

Use `-v` for verbose output and `-t` (`--task`) with a comma-separated list to
limit the run to specific tasks. Add `-d` (`--dry-run`) to simulate.

See [Illuminator](/illuminator/).

## Recommended composer Scripts

Group commands so they are easy to remember and easy to wire into hooks or CI:

```json
"scripts": {
    "setup": "bin/cake generate code_completion && bin/cake generate phpstorm",
    "annotate": "bin/cake annotate all && bin/cake annotate all -p Sandbox",
    "illuminate": "bin/cake illuminate code"
}
```

That way you only have to remember the wrapper commands:

- `composer setup` — also useful as a Git hook after checkout/pull
- `composer annotate` — include all your `/plugins/` (the non-vendor ones)
- `composer illuminate` — include all your `/plugins/` (the non-vendor ones)
