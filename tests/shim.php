<?php

use PHP_CodeSniffer\Config;
use PHPUnit\Framework\TestCase;

$manualAutoload = getcwd() . '/vendor/squizlabs/php_codesniffer/autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}

error_reporting(E_ALL & ~E_USER_DEPRECATED);

if (!defined('T_NULLABLE')) {
	define('T_NULLABLE', 'PHPCS_T_NULLABLE');
}
if (!defined('T_DOC_COMMENT_TAG')) {
	define('T_DOC_COMMENT_TAG', 'PHPCS_T_DOC_COMMENT_TAG');
}
if (!defined('T_SEMICOLON')) {
	define('T_SEMICOLON', 'PHPCS_T_SEMICOLON');
}

if (!class_exists(TestCase::class)) {
	require 'TestCase.php';
}
