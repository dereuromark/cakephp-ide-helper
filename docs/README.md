#  CakePHP IdeHelper Plugin Documentation

## Install
Install it as `require-dev` dependency:
```
composer require --dev dereuromark/cakephp-ide-helper
```

## Setup
Enable the plugin in your `config/bootstrap_cli.php` or call
```
bin/cake plugin load IdeHelper --cli
```

Note: As require-dev dependency this should only be loaded for local development (include a check or try/catch).

## Overview

### Annotations
Update baked annotations and provide maximum IDE and static analyzer (e.g. phpstan) support.

* [Annotations shell](Annotations.md)

### Code completion file
Create a code completion file for the IDE in order to annotate dynamic "magic" property access and alike.
This removes the need for inline annotations all over the CakePHP code, as the IDE then automatically understands, type-hints and auto-completes here.

* [CodeCompletion shell](CodeCompletion.md)

### Meta file
Create a meta file for the IDE in order to hint static factory methods and alike, that otherwise
would always just return the abstract or parent class.

* [Generator shell](Generator.md)

### PHP File Illuminator
The Illuminator can modify your PHP files based on Illuminator rulesets.
You can use the pre-set tasks, or create your own to enhance your PHP files and classes.

* [Illuminator shell](Illuminator.md)


## Usage
Quick-Guide, see the above links for details.

### Using the annotations shell
Running it on your app:
```
bin/cake annotations [type]
```
By default it will print out a diff of the changes incl the amount of modified lines if applicable.

Running it on an installed plugin:
```
bin/cake annotations [type] -p FooBar
```
Note: It will be autoloaded if needed (if not manually loaded already).

Use `-v` for verbose and detailed output:
```
bin/cake annotations [type] -v
```

You can add `-d` (`--dry-run`) to simulate the output without actually modifying the files.

### Using the code completion shell
The code completion file aims to be generic and to work with all IDEs.

Generate your code completion files into TMP:
```
bin/cake code_completion generate
```

### Using the generator shell
So far the meta file is available for the following IDEs:
- PhpStorm (2016.2+)

Generate your app `.phpstorm.meta.php` meta file:
```
bin/cake phpstorm generate
```

### Using the illuminator shell
Improve your PHP files:
```
bin/cake illuminator illuminate <path>
```

Use `-v` for verbose and detailed output:
```
bin/cake illuminator illuminate <path> -v
```

Use `-t` (`--task`) to only run specific task(s), can be a comma separated list.

You can add `-d` (`--dry-run`) to simulate the output without actually modifying the files.


## Tips

Group them for your project as composer.json script commands:
```
"scripts": {
    ...
    "setup": "bin/cake code_completion generate && bin/cake phpstorm generate",
    "annotations": "bin/cake annotations all && bin/cake annotations all -p Sandbox && ...",
```

This way you only need to remember these high level ones:
- `composer setup` (also possible as Git hook after checkout/pull)
- `composer annotations` (include all your /plugins, the non-vendor ones)

