<?php

use PHP_CodeSniffer\Config;

$manualAutoload = getcwd() . '/vendor/squizlabs/php_codesniffer/autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}

error_reporting(E_ALL & ~E_USER_DEPRECATED);

if (!defined('T_NULLABLE')) {
	define('T_NULLABLE', 'PHPCS_T_NULLABLE');
}

if (!class_exists(\PHPUnit\Framework\TestCase::class)) {
	require 'TestCase.php';
}
