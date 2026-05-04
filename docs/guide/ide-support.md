# IDE Support

This plugin is intended to work with **any IDE** that supports annotations and
code completion. Below is the current state of testing and integration.

## Tested IDEs

IDEs tested for full compatibility:

- **[PhpStorm](https://github.com/dereuromark/cakephp-ide-helper/wiki/PHPStorm)** — also supports the meta file generator
- IntelliJ IDEA
- Atom
- **[VS Code](https://github.com/dereuromark/cakephp-ide-helper/wiki/Visual-Studio-Code)** — meta file works via the PHP Intelephense plugin
- Report or open a PR for your IDE on the [wiki](https://github.com/dereuromark/cakephp-ide-helper/wiki) to confirm full compatibility.

## Plugins With Meta File Generator Tasks

The following plugins ship Generator tasks that build on top of this plugin:

- [Migrations](https://github.com/cakephp/migrations) — migration file writing (included in IdeHelper directly).
- [Queue](https://github.com/dereuromark/cakephp-queue) — `QueuedJobsTable::createJob()` usage.
- [Burzum/CakeServiceLayer](https://github.com/burzum/cakephp-service-layer) — `loadService()` usage.
- [CakephpFixtureFactories](https://github.com/vierge-noire/cakephp-fixture-factories) — factory class autocomplete.

Add yours via PR.

## Plugins With Annotator Tasks

- See the [IdeHelperExtra](https://github.com/dereuromark/cakephp-ide-helper-extra) plugin for a curated collection of additional annotator tasks.

## Plugins With Illuminator Tasks

- [StateMachine](https://github.com/spryker/cakephp-statemachine) — syncs states from XML into PHP.

## Sponsorship

[![PhpStorm logo.](https://resources.jetbrains.com/storage/products/company/brand/logos/PhpStorm.svg)](https://jb.gg/OpenSourceSupport)

JetBrains sponsors PhpStorm for the FOSS work on this repository and beyond.
