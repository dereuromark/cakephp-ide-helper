# Contributing

## Run the checks

Install the development dependencies at the repository root, then run:

```bash
composer cs-check
composer test
composer stan
```

Use `composer cs-fix` to apply coding standard fixes. CI also runs Rector:

```bash
composer rector-setup
composer rector
```

`rector-setup` changes the local development dependencies before the dry-run
check. Review the generated diff if you use `composer rector-fix`.

## Update a metadata fixture

The PhpStorm generator test selects a fixture for the installed CakePHP
version. To overwrite that selected fixture with the current generated output:

```bash
vendor/bin/phpunit tests/TestCase/Generator/PhpstormGeneratorTest.php --debug
```

This writes directly to `tests/test_files/meta/phpstorm/`. Review the fixture
diff and test against the supported CakePHP versions before committing it.
An ordinary test run leaves the fixtures unchanged.
