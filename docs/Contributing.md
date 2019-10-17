# Contributing

## Basics
See composer scripts for
```
composer cs-check
composer cs-fix
composer test-setup
composer test
```
etc

## Generator
 
### New meta file

Run the test with the `--debug` option to generate a new `TMP/.meta.php`:
```
php phpunit.phar tests/TestCase/Generator/PhpstormGeneratorTest.php --debug
```
This way you can easily copy it over into the test_files/ directory and replace the existing one.
