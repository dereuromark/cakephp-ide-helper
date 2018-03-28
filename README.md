#  CakePHP IdeHelper Plugin

[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-ide-helper.png?branch=master)](https://travis-ci.org/dereuromark/cakephp-ide-helper)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/cakephp-ide-helper/master.svg)](https://codecov.io/github/dereuromark/cakephp-ide-helper?branch=master)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-ide-helper/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-ide-helper/license.png)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-ide-helper/d/total.png)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)

IdeHelper plugin for CakePHP applications.

> Boost your productivity. Avoid mistakes.

**This branch is for CakePHP 3.5+**

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
- Controllers (including prefixes like `Admin`)
- View (AppView)
- Templates (`.ctp` files including elements)
- Shells (and Tasks)
- Components
- Helpers

![Screenshot](docs/screenshot.jpg)

Supports code completion help for:
- Behaviors (property access on the BehaviorRegistry)

### IDE support
This plugin is supposed to work with ANY IDE that supports annotations and code completion.
IDEs tested so far for 100% compatibility:
- **[PHPStorm](https://github.com/dereuromark/cakephp-ide-helper/wiki/PHPStorm)** (incl. meta file generator)
- IntelliJ
- Atom
- ... [Report or PR your IDE of choice here to confirm its full compatibility]

See [Wiki](https://github.com/dereuromark/cakephp-ide-helper/wiki) for details and tips/settings.

### Plugins with meta file generator tasks
The following plugins use this plugin to improve IDE compatibility around factory and magic string usage:
- **[Queue](https://github.com/dereuromark/cakephp-queue)** for `QueuedJobsTable::createJob()` usage.
- ... (add yours here)

### Install, Setup, Usage
See the **[Docs](https://github.com/dereuromark/cakephp-ide-helper/tree/master/docs)** for details.
