<?php

namespace IdeHelper\Command;

use Cake\Command\Command as CoreCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\Utility\PluginPath;

abstract class Command extends CoreCommand {

	/**
	 * @var \Cake\Console\Arguments
	 */
	protected Arguments $args;

	/**
	 * @var \Cake\Console\ConsoleIo
	 */
	protected ConsoleIo $io;

	protected ?string $plugin = null;

	/**
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @throws \Cake\Console\Exception\StopException
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		$this->args = $args;
		$this->io = $io;

		parent::execute($args, $io);
	}

	/**
	 * @param string|null $type
	 * @return array<string, array<string>>
	 */
	protected function getPaths(?string $type = null): array {
		$plugin = (string)$this->args->getOption('plugin') ?: null;
		if (!$plugin) {
			if (!$type) {
				$paths = [ROOT . DS];
			} elseif ($type === 'classes') {
				$paths = [ROOT . DS . APP_DIR . DS];
			} else {
				$paths = $type === 'templates' ? App::path('templates') : AppPath::get($type);
			}

			return ['app' => $paths];
		}

		$plugins = $this->getPlugins($plugin);

		$paths = [];
		foreach ($plugins as $plugin) {
			if (!$type) {
				$pluginPaths = [Plugin::path($plugin)];
			} else {
				if ($type === 'classes') {
					$pluginPaths = [PluginPath::classPath($plugin)];
				} else {
					$pluginPaths = $type === 'templates' ? App::path('templates', $plugin) : AppPath::get($type, $plugin);
				}
			}

			$paths[$plugin] = $pluginPaths;
		}

		return $paths;
	}

	/**
	 * @param string $plugin
	 *
	 * @return array<string>
	 */
	protected function getPlugins(string $plugin): array {
		if ($plugin !== 'all' && !str_contains($plugin, '*')) {
			return [Plugin::path($plugin) => $plugin];
		}

		$loaded = Plugin::loaded();
		$plugins = [];
		foreach ($loaded as $name) {
			$path = Plugin::path($name);
			$rootPath = str_replace(ROOT . DS, '', $path);
			if (str_starts_with($rootPath, 'vendor' . DS)) {
				continue;
			}

			$plugins[$path] = $name;
		}

		if ($plugin === 'all') {
			return $plugins;
		}

		return $this->filterPlugins($plugins, $plugin);
	}

	/**
	 * @param array<string> $plugins
	 * @param string $pattern
	 * @return array<string>
	 */
	protected function filterPlugins(array $plugins, string $pattern): array {
		return array_filter($plugins, function($plugin) use ($pattern) {
			return fnmatch($pattern, $plugin);
		});
	}

	/**
	 * @param string $plugin
	 * @return void
	 */
	protected function setPlugin(string $plugin): void {
		if (!$plugin || $plugin === 'app') {
			$this->plugin = null;

			return;
		}

		$this->plugin = $plugin;
	}

}
