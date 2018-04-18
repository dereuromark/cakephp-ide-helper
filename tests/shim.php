<?php
use PHP_CodeSniffer\Config;

$manualAutoload = getcwd() . '/vendor/squizlabs/php_codesniffer/autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}

error_reporting(E_ALL & ~E_USER_DEPRECATED);
