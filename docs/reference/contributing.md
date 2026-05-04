# Contributing

## Basics

See the composer scripts:

```bash
composer cs-check
composer cs-fix
composer test-setup
composer test
```

## Generator

### New meta file

Run the test with the `--debug` option to generate a new `TMP/.meta.php`:

```bash
php phpunit.phar tests/TestCase/Generator/PhpstormGeneratorTest.php --debug
```

You can then easily copy it over into the `test_files/` directory and replace
the existing one.
