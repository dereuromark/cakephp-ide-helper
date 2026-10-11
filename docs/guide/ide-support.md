# IDE Support

Annotations work in editors and static analyzers that understand PHPDoc.
[Code completion stubs](/code-completion/) need an editor that indexes the
application's `tmp/` files.

## Metadata support

- [PhpStorm setup](https://github.com/dereuromark/cakephp-ide-helper/wiki/PHPStorm)
- [VS Code with Intelephense](https://github.com/dereuromark/cakephp-ide-helper/wiki/Visual-Studio-Code)

These editors can use the [metadata generator](/generator/). Reindex the
generated files if completion does not update. Editor versions and plugins
can differ in their interpretation of advanced generic annotations; verify
completion for the types used in your application.

## Plugins With Meta File Generator Tasks

The following plugins ship Generator tasks that build on top of this plugin:

- [Migrations](https://github.com/cakephp/migrations): migration file writing (included in IdeHelper directly).
- [Queue](https://github.com/dereuromark/cakephp-queue): `QueuedJobsTable::createJob()` usage.
- [Burzum/CakeServiceLayer](https://github.com/burzum/cakephp-service-layer): `loadService()` usage.
- [CakephpFixtureFactories](https://github.com/vierge-noire/cakephp-fixture-factories): factory class autocomplete.

Add yours via PR.

## Plugins With Annotator Tasks

- See the [IdeHelperExtra](https://github.com/dereuromark/cakephp-ide-helper-extra) plugin for a curated collection of additional annotator tasks.

## Plugins With Illuminator Tasks

- [StateMachine](https://github.com/spryker/cakephp-statemachine): syncs states from XML into PHP.

## Sponsorship

[![PhpStorm logo.](https://resources.jetbrains.com/storage/products/company/brand/logos/PhpStorm.svg)](https://jb.gg/OpenSourceSupport)

JetBrains sponsors PhpStorm for the FOSS work on this repository and beyond.
