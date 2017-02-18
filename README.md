#  CakePHP IdeHelper Plugin

[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-ide-helper.png?branch=master)](https://travis-ci.org/dereuromark/cakephp-ide-helper)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/cakephp-ide-helper/master.svg)](https://codecov.io/github/dereuromark/cakephp-ide-helper?branch=master)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-ide-helper/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.5-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-ide-helper/license.png)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-ide-helper/d/total.png)](https://packagist.org/packages/dereuromark/cakephp-ide-helper)

IdeHelper engine for CakePHP applications.

**This branch is for CakePHP 3.x**

Note: Highly unstable still at this point.

## Features

The main idea is to improve IDE compatability and use annotations to make the IDE understand the
"magic" of CakePHP, so you can click through the classes and object chains as well as see obvious issues and mistakes.
The IDE will usually mark problematic code yellow (missing, wrong method etc).

So for now:
- Annotate existing classes (e.g. when upgrading an application)

## Install
```
composer require dereuromark/cakephp-ide-helper:dev-master
```

## Setup
Enable the plugin in your `config/bootstrap.php` or call
```
bin/cake plugin load IdeHelper
```

### Using the annotation shell
Running it on your app:
```
bin/cake annotation controllers -v
```
Use `-v` for verbose and detailed output.

Running it on a loaded plugin:
```
bin/cake annotation controllers -p FooBar
```

You can use `-d` (`--dry-run`) to simulate the output without actually modifying the files.
