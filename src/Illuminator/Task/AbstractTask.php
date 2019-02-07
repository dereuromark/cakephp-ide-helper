<?php

namespace IdeHelper\Illuminator\Task;

use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use IdeHelper\Annotator\Traits\FileTrait;
use PHP_CodeSniffer\Config;

$composerVendorDir = getcwd() . DS . 'vendor';
$codesnifferDir = 'squizlabs' . DS . 'php_codesniffer';
if (!is_dir($composerVendorDir . DS . $codesnifferDir)) {
	$ideHelperDir = substr(__DIR__, 0, strpos(__DIR__, DS . 'cakephp-ide-helper'));
	$composerVendorDir = dirname($ideHelperDir);
}
$manualAutoload = $composerVendorDir . DS . $codesnifferDir . DS . 'autoload.php';
if (!class_exists(Config::class) && file_exists($manualAutoload)) {
	require $manualAutoload;
}

/**
 * Reads the annotated properties of an entity and adds the constants based on those.
 */
abstract class AbstractTask {

	use FileTrait;
	use InstanceConfigTrait;

	const CONFIG_DRY_RUN = 'dry-run';
	const CONFIG_PLUGIN = 'plugin';
	const CONFIG_NAMESPACE = 'namespace';
	const CONFIG_VERBOSE = 'verbose';

	const COUNT_ADDED = 'added';

	/**
	 * @var array
	 */
	protected $_defaultConfig = [
	];

	/**
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->setConfig($config);

		$namespace = $this->getConfig(static::CONFIG_PLUGIN) ?: Configure::read('App.namespace', 'App');
		$namespace = str_replace('/', '\\', $namespace);
		$this->setConfig(static::CONFIG_NAMESPACE, $namespace);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	abstract public function isApplicable($path);

	/**
	 * @param string $content
	 * @param string $path
	 * @return string
	 */
	abstract public function run($content, $path);

}
