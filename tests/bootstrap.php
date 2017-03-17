<?php

use Cake\Routing\DispatcherFactory;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');

// Point app constants to the test app.
define('TEST_ROOT', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('APP', TEST_ROOT . APP_DIR . DS);
define('TEST_FILES', ROOT . DS . 'tests' . DS . 'test_files' . DS);

define('TMP', ROOT . DS . 'tmp' . DS);
if (!is_dir(TMP)) {
	mkdir(TMP, 0770, true);
}
define('CONFIG', ROOT . DS . 'config' . DS);

define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);

define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . APP_DIR . DS);

require dirname(__DIR__) . '/vendor/autoload.php';
require CORE_PATH . 'config/bootstrap.php';

Cake\Core\Configure::write('App', [
	'namespace' => 'App',
	'paths' => [
		'templates' => [APP . 'Template' . DS],
	]
]);

Cake\Core\Configure::write('debug', true);

$cache = [
	'default' => [
		'engine' => 'File'
	],
	'_cake_core_' => [
		'className' => 'File',
		'prefix' => 'crud_myapp_cake_core_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds'
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'crud_my_app_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds'
	]
];

Cake\Cache\Cache::config($cache);

Cake\Core\Plugin::load('IdeHelper', ['path' => ROOT . DS, 'autoload' => true]);
Cake\Core\Plugin::load('Shim', ['path' => ROOT . DS . 'vendor/dereuromark/cakephp-shim/', 'autoload' => true]);
Cake\Core\Plugin::load('Awesome', ['path' => TEST_ROOT . 'plugins/Awesome/', 'autoload' => true]);

DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');

// Ensure default test connection is defined
if (!getenv('db_class')) {
	putenv('db_class=Cake\Database\Driver\Sqlite');
	putenv('db_dsn=sqlite::memory:');
}

Cake\Datasource\ConnectionManager::config('test', [
	'className' => 'Cake\Database\Connection',
	'driver' => getenv('db_class'),
	'dsn' => getenv('db_dsn'),
	'database' => getenv('db_database'),
	'username' => getenv('db_username'),
	'password' => getenv('db_password'),
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);

Cake\Datasource\ConnectionManager::config('test_database_log', [
	'className' => 'Cake\Database\Connection',
	'driver' => getenv('db_class'),
	'dsn' => getenv('db_dsn'),
	'database' => getenv('db_database'),
	'username' => getenv('db_username'),
	'password' => getenv('db_password'),
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);
