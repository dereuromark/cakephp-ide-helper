#  CakePHP IdeHelper Plugin

[![CI](https://github.com/dereuromark/cakephp-ide-helper/workflows/CI/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-ide-helper/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/cakephp-ide-helper/master.svg)](https://app.codecov.io/github/dereuromark/cakephp-ide-helper/tree/master)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-ide-helper/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-ide-helper/license.png)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-ide-helper/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)

IdeHelper plugin for CakePHP applications.

> Boost your productivity. Avoid mistakes.

This branch is for use with **CakePHP 4.2+**. For details see [version map](https://github.com/dereuromark/cakephp-ide-helper/wiki#cakephp-version-map).

## Features

The main idea is to improve IDE compatibility and use annotations to make the IDE understand the
"magic" of CakePHP, so you can click through the class methods and object chains as well as spot obvious issues and mistakes easier. The IDE will usually mark problematic code yellow (missing, wrong method etc).

This also improves compatibility with tools like [PHPStan](https://github.com/phpstan/phpstan).
Those can then follow the code easier and provide more valuable help.

- Add annotations to existing code (e.g. when upgrading an application) just like baking would to new code.
- Can run multiple times without adding the annotations again.
- It can also replace or remove outdated annotations.
- Works with your application as well as any loaded plugin.
- CI check support, hook it up just like the coding standards check.

Supports annotations for:
- Models (Tables and Entities)
- Controllers (including prefixes like `Admin`) and Components
- View (AppView) and Helpers
- Templates (`.php` PHP template files including elements)
- Commands, Shells and Tasks
- ... and more

![Screenshot](docs/screenshot.jpg)

Supports code completion help for:
- Behaviors (property access on the BehaviorRegistry)

Supports IDE autocomplete/typehinting of (magic)strings as well as return types/values for:
- Plugins, Components, Behaviors, Helpers, Mailers
- Associations, Validation
- I18n Translation, Cache
- Elements and layouts
- Tables and their fields
- Route paths, Request/ENV, Connection
- ... and more (using PhpStorm meta file)

Supports better IDE usage with Illuminator tasks to enhance existing code:
- EntityFieldTask adds all entity fields as class constants for easier usage in IDEs

### IDE support
This plugin is supposed to work with ANY IDE that supports annotations and code completion.
IDEs tested so far for 100% compatibility:
- **[PhpStorm](https://github.com/dereuromark/cakephp-ide-helper/wiki/PHPStorm)** (incl. meta file generator)
- IntelliJ
- Atom
- **[VS Code](https://github.com/dereuromark/cakephp-ide-helper/wiki/Visual-Studio-Code)**
- ... [Report or PR your IDE of choice here to confirm its full compatibility]

See [Wiki](https://github.com/dereuromark/cakephp-ide-helper/wiki) for details and tips/settings.

### Plugins with meta file generator tasks
The following plugins use this plugin to improve IDE compatibility around factory and magic string usage:
- [Migrations](https://github.com/cakephp/migrations) for migration file writing (included in IdeHelper directly).
- [Queue](https://github.com/dereuromark/cakephp-queue) for `QueuedJobsTable::createJob()` usage.
- [Burzum/CakeServiceLayer](https://github.com/burzum/cakephp-service-layer) for `loadService()` usage.
- ... (add yours here)

### Plugins with Illuminator tasks
- [StateMachine](https://github.com/spryker/cakephp-statemachine) for syncing states from XML into PHP.
- ... (add yours here)

### More
More collections of useful tasks can be found in the [IdeHelperExtra plugin](https://github.com/dereuromark/cakephp-ide-helper-extra).

### Install, Setup, Usage
See the **[Docs](docs/)** for details.
