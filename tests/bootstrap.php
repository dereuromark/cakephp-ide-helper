<?php

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\Fixture\SchemaLoader;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define('PLUGIN_ROOT', dirname(__DIR__));
define('APP_DIR', 'src');

// Point app constants to the test app.
define('ROOT', PLUGIN_ROOT . DS . 'tests' . DS . 'test_app');
define('APP', ROOT . DS . APP_DIR . DS);
define('PLUGINS', ROOT . DS . 'plugins' . DS);
define('TEST_FILES', PLUGIN_ROOT . DS . 'tests' . DS . 'test_files' . DS);

define('TMP', PLUGIN_ROOT . DS . 'tmp' . DS);
if (!is_dir(TMP)) {
	mkdir(TMP, 0770, true);
}
define('CONFIG', PLUGIN_ROOT . DS . 'config' . DS);

define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);

define('CAKE_CORE_INCLUDE_PATH', PLUGIN_ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . APP_DIR . DS);

require dirname(__DIR__) . '/vendor/autoload.php';
require CORE_PATH . 'config/bootstrap.php';
require CAKE . 'functions.php';

Configure::write('App', [
	'namespace' => 'TestApp',
	'encoding' => 'utf-8',
	'paths' => [
		'templates' => [ROOT . DS . 'templates' . DS],
	],
]);
Configure::write('debug', true);

$cache = [
	'default' => [
		'engine' => 'File',
	],
	'_cake_translations_' => [
		'className' => 'File',
		'prefix' => 'myapp_cake_translations_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds',
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'myapp_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds',
	],
];

Cache::setConfig($cache);

TypeFactory::build('time');
TypeFactory::build('date');
TypeFactory::build('datetime');
TypeFactory::build('timestamp');

class_alias(Controller::class, 'App\Controller\AppController');

// Ensure default test connection is defined
if (!getenv('DB_URL')) {
	putenv('DB_URL=sqlite:///:memory:');
}

ConnectionManager::setConfig('test', [
	'url' => getenv('DB_URL'),
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);

if (env('FIXTURE_SCHEMA_METADATA')) {
	$loader = new SchemaLoader();
	$loader->loadInternalFile(env('FIXTURE_SCHEMA_METADATA'));
}
