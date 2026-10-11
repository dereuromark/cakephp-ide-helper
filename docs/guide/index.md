# Introduction

`cakephp-ide-helper` improves IDE compatibility and uses annotations to make
your IDE understand the "magic" of CakePHP, so you can click through class
methods and object chains and spot obvious issues and mistakes earlier. It
also improves compatibility with tools like [PHPStan](https://github.com/phpstan/phpstan).

For supported dependencies see [Installation](./installation). For older CakePHP
versions see the
[version map](https://github.com/dereuromark/cakephp-ide-helper/wiki#cakephp-version-map).

## Two Tools, One Plugin

There are two main tools for keeping your code up to date:

| Tool | Modifies | When to use |
|------|----------|-------------|
| **Annotator** | Doc blocks and annotations only: never functional code | Keep type hints in sync as the codebase evolves |
| **Illuminator** | Functional code itself (constants, methods, etc.) | One-shot rewrites: for example, add entity field constants |

On top of those there are two stub-file generators:

| Generator | Output | Audience |
|-----------|--------|----------|
| **Meta File Generator** | `.phpstorm.meta.php/.ide-helper.meta.php` | PhpStorm and VS Code (Intelephense) |
| **Code Completion Generator** | Generic PHP stubs in `TMP/` | Any IDE that indexes PHP files |

Annotations are needed for static analyzers to understand the code; the meta
file and code completion stubs are mainly IDE autocomplete helpers.

## What's Next

1. [Installation](./installation): Get the plugin set up
2. [Getting started](./getting-started)
3. [Usage](./usage): High-level commands and recommended composer scripts
4. [IDE Support](./ide-support): IDE setup and plugin extensions
5. [Annotations](/annotations/): Per-class-type annotation reference
6. [Code Completion](/code-completion/), [Generator](/generator/), [Illuminator](/illuminator/)

If you are upgrading from 4.x see [Migrating from 4.x](./migration).
