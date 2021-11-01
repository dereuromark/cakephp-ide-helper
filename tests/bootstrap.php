<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');

// Point app constants to the test app.
define('TEST_ROOT', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('APP', TEST_ROOT . APP_DIR . DS);
define('PLUGINS', TEST_ROOT . 'plugins' . DS);
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

Configure::write('App', [
	'namespace' => 'TestApp',
	'encoding' => 'utf-8',
	'paths' => [
		'templates' => [TEST_ROOT . 'templates' . DS],
	],
]);
Configure::write('debug', true);

$cache = [
	'default' => [
		'engine' => 'File',
	],
	'_cake_core_' => [
		'className' => 'File',
		'prefix' => 'crud_myapp_cake_core_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds',
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'crud_my_app_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds',
	],
];

Cache::setConfig($cache);

Type::build('time');
Type::build('date');
Type::build('datetime');
Type::build('timestamp');

class_alias(Cake\Controller\Controller::class, 'App\Controller\AppController');

Plugin::getCollection()->add(new IdeHelper\Plugin());
Plugin::getCollection()->add(new Shim\Plugin());
Plugin::getCollection()->add(new Awesome\Plugin());
Plugin::getCollection()->add(new Controllers\Plugin());
Plugin::getCollection()->add(new Relations\Plugin());
Plugin::getCollection()->add(new MyNamespace\MyPlugin\Plugin());

if (getenv('db_dsn')) {
	Cake\Datasource\ConnectionManager::setConfig('test', [
		'className' => 'Cake\Database\Connection',
		'url' => getenv('db_dsn'),
		'timezone' => 'UTC',
		'quoteIdentifiers' => true,
		'cacheMetadata' => true,
	]);

	return;
}

// Ensure default test connection is defined
if (!getenv('db_dsn')) {
	putenv('db_dsn=sqlite:///:memory:');

	//putenv('db_dsn=postgres://postgres@127.0.0.1/test');
}

Cake\Datasource\ConnectionManager::setConfig('test', [
	'url' => getenv('db_dsn'),
	'driver' => getenv('db_class'),
	'database' => getenv('db_database'),
	'username' => getenv('db_username'),
	'password' => getenv('db_password'),
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);
