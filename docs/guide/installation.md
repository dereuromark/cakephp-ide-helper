# Installation

This branch uses unreleased CakePHP 6 packages. Set `"minimum-stability": "dev"`
and `"prefer-stable": true` in your application's `composer.json` before installing.

Install the plugin in your application's development dependencies:

```bash
composer require --dev dereuromark/cakephp-ide-helper:dev-cake6
```

Composer checks the supported PHP and CakePHP versions against your application.
See the [package constraints](https://github.com/dereuromark/cakephp-ide-helper/blob/cake6/composer.json)
for the current requirements.

## Load the commands

In `src/Application.php`, add this inside `bootstrap()`, after
`parent::bootstrap()`:

```php
if (PHP_SAPI === 'cli' && class_exists(\IdeHelper\IdeHelperPlugin::class)) {
    $this->addPlugin('IdeHelper');
}
```

This registers the commands for CLI use when the development dependency is
installed. The class check also permits deployments using `composer install
--no-dev` to boot without the plugin.

## Check the commands

From the application directory, run:

```bash
bin/cake annotate all --help
bin/cake generate code_completion --help
bin/cake generate phpstorm --help
bin/cake illuminate code --help
```

Continue with [Getting started](./getting-started) for a first annotation preview
and IDE setup.
