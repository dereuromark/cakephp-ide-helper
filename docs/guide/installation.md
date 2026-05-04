# Installation

## Composer

Install as a `require-dev` dependency:

```bash
composer require --dev dereuromark/cakephp-ide-helper
```

## Plugin Loading

Enable the plugin in your `Application.php` or call:

```bash
bin/cake plugin load IdeHelper
```

::: tip Local-only loading
As a `require-dev` dependency, the plugin should only load for local
development. Wrap the registration with a check or `try`/`catch`, and ideally
also restrict it to CLI mode (`if (PHP_SAPI === 'cli')`).
:::

## Verifying the Install

Once the plugin is loaded, the following commands become available:

```bash
bin/cake annotate --help
bin/cake generate --help
bin/cake illuminate --help
```

If those resolve, you are ready to move on to [Usage](./usage).
