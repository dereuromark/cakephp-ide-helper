<?php

namespace IdeHelper\Utility;

use Cake\Core\BasePlugin;
use Cake\Core\ClassLoader;
use Cake\Core\Plugin as CorePlugin;

class Plugin extends CorePlugin {

	/**
	 * Shim until core
	 *
	 * @param string $plugin name of the plugin to be loaded in CamelCase format.
	 * @param array $config Configuration options for the plugin.
	 * @throws \Cake\Core\Exception\MissingPluginException if the folder for the plugin to be loaded is not found
	 * @return void
	 */
	public static function load(string $plugin, array $config = []): void {
		$config += [
			'autoload' => false,
			'bootstrap' => false,
			'routes' => false,
			'console' => true,
			'classBase' => 'src',
			'ignoreMissing' => false,
			'name' => $plugin
		];

		if (!isset($config['path'])) {
			$config['path'] = static::getCollection()->findPath($plugin);
		}

		$config['classPath'] = $config['path'] . $config['classBase'] . DIRECTORY_SEPARATOR;
		if (!isset($config['configPath'])) {
			$config['configPath'] = $config['path'] . 'config' . DIRECTORY_SEPARATOR;
		}
		$pluginClass = str_replace('/', '\\', $plugin) . '\\Plugin';
		if (class_exists($pluginClass)) {
			$instance = new $pluginClass($config);
		} else {
			// Use stub plugin as this method will be removed long term.
			$instance = new BasePlugin($config);
		}
		static::getCollection()->add($instance);

		if ($config['autoload'] === true) {
			if (empty(static::$_loader)) {
				static::$_loader = new ClassLoader();
				static::$_loader->register();
			}
			static::$_loader->addNamespace(
				str_replace('/', '\\', $plugin),
				$config['path'] . $config['classBase'] . DIRECTORY_SEPARATOR
			);
			static::$_loader->addNamespace(
				str_replace('/', '\\', $plugin) . '\Test',
				$config['path'] . 'tests' . DIRECTORY_SEPARATOR
			);
		}

		if ($config['bootstrap'] === true) {
			static::bootstrap($plugin);
		}
	}

	/**
	 * Loads the bootstrapping files for a plugin, or calls the initialization setup in the configuration
	 *
	 * @param string $name name of the plugin
	 * @return mixed
	 * @see \Cake\Core\Plugin::load() for examples of bootstrap configuration
	 */
	public static function bootstrap(string $name) {
		$plugin = static::getCollection()->get($name);
		if (!$plugin->isEnabled('bootstrap')) {
			return false;
		}
		// Disable bootstrapping for this plugin as it will have
		// been bootstrapped.
		$plugin->disable('bootstrap');

		return static::_includeFile(
			$plugin->getConfigPath() . 'bootstrap.php',
			true
		);
	}

	/**
	 * Include file, ignoring include error if needed if file is missing
	 *
	 * @param string $file File to include
	 * @param bool $ignoreMissing Whether to ignore include error for missing files
	 * @return mixed
	 */
	protected static function _includeFile(string $file, bool $ignoreMissing = false) {
		if ($ignoreMissing && !is_file($file)) {
			return false;
		}

		return include $file;
	}

}
