<?php

namespace IdeHelper\Generator\Task;

use Cake\Console\ConsoleIo;
use IdeHelper\Filesystem\Folder;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;

class ConsoleHelperTask implements TaskInterface {

	public const CLASS_CONSOLE_IO = ConsoleIo::class;

	/**
	 * @var array<string>
	 */
	protected array $loadAliases = [
		'\\' . self::CLASS_CONSOLE_IO . '::helper(0)',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$addMap = [];

		$components = $this->collectHelpers();
		foreach ($components as $name => $className) {
			$addMap[$name] = ClassName::create($className);
			if (str_contains($name, '.')) {
				[, $name] = pluginSplit($name);
			}
		}

		ksort($addMap);

		$result = [];
		foreach ($this->loadAliases as $alias) {
			$directive = new Override($alias, $addMap);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectHelpers(): array {
		$helpers = [];

		$folders = array_merge(App::core('Command/Helper'), AppPath::get('Command/Helper'));
		foreach ($folders as $folder) {
			$helpers = $this->addHelpers($helpers, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Command/Helper', $plugin);
			foreach ($folders as $folder) {
				$helpers = $this->addHelpers($helpers, $folder, $plugin);
			}
		}

		return $helpers;
	}

	/**
	 * @param array<string> $helpers
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string>
	 */
	protected function addHelpers(array $helpers, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Helper\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'Command/Helper', 'Helper');
			if (!$className) {
				continue;
			}

			$helpers[$name] = $className;
		}

		return $helpers;
	}

}
