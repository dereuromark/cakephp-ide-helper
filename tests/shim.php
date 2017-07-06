<?php
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Util\Tokens;

$manualAutoload = getcwd() . '/vendor/squizlabs/php_codesniffer/autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}
if (!class_exists(Runner::class)) {
	class_alias('\PHP_CodeSniffer_File', File::class);
	class_alias('\PHP_CodeSniffer_Fixer', Fixer::class);
	class_alias('\PHP_CodeSniffer_Tokens', Tokens::class);
}
