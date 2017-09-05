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

* [Annotations shell](https://github.com/dereuromark/cakephp-ide-helper/tree/master/docs/Annotations.md)

### Meta file
Create a meta file for the IDE in order to hint static factory methods and alike, that otherwise
would always just return the abstract or parent class.

* [Generator shell](https://github.com/dereuromark/cakephp-ide-helper/tree/master/docs/Generator.md)

## Usage
Quick-Guide, see the above links for details.

### Using the generator shell
So far the meta file is available for the following IDEs:
- PhpStorm (2016.2+)

Generate your app `.phpstorm.meta.php` meta file:
```
bin/cake phpstorm generate
```

### Using the annotations shell
Running it on your app:
```
bin/cake annotations [type]
```
By default it will print out a diff of the changes incl the amount of modified lines if applicable.

Running it on a loaded plugin:
```
bin/cake annotations [type] -p FooBar
```

Use `-v` for verbose and detailed output:
```
bin/cake annotations [type] -v
```

You can add `-d` (`--dry-run`) to simulate the output without actually modifying the files.
