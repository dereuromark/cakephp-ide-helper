<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\View\ViewBuilder;
use DirectoryIterator;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\StringName;
use RecursiveRegexIterator;
use RegexIterator;

class LayoutTask implements TaskInterface {

	public const CLASS_VIEW_BUILDER = ViewBuilder::class;

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$layouts = $this->collectLayouts();
		$list = [];
		foreach ($layouts as $layout) {
			$list[$layout] = StringName::create($layout);
		}

		ksort($list);

		$method = '\\' . static::CLASS_VIEW_BUILDER . '::setLayout()';
		$directive = new ExpectedArguments($method, 0, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectLayouts(): array {
		$paths = App::path('templates');

		$result = [];
		$result = $this->addLayouts($result, $paths);

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$paths = App::path('templates', $plugin);
			$result = $this->addLayouts($result, $paths, $plugin);
		}

		sort($result);

		return $result;
	}

	/**
	 * @param array<string> $result
	 * @param array<string> $paths
	 * @param string|null $plugin
	 *
	 * @return array<string>
	 */
	protected function addLayouts(array $result, array $paths, ?string $plugin = null): array {
		foreach ($paths as $path) {
			$path .= 'layout' . DS;
			if (!is_dir($path)) {
				continue;
			}

			$Directory = new DirectoryIterator($path);
			$Regex = new RegexIterator($Directory, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

			foreach ($Regex as $file) {
				$name = str_replace($path, '', $file[0]);
				$name = substr($name, 0, -4);
				$name = str_replace(DS, '/', $name);
				if ($plugin) {
					$name = $plugin . '.' . $name;
				}
				$result[] = $name;
			}
		}

		return $result;
	}

}
