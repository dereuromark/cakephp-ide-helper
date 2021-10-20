<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\View\View;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\StringName;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class ElementTask implements TaskInterface {

	public const CLASS_VIEW = View::class;

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$elements = $this->collectElements();
		$list = [];
		foreach ($elements as $element) {
			$list[$element] = StringName::create($element);
		}

		ksort($list);

		$method = '\\' . static::CLASS_VIEW . '::element()';
		$directive = new ExpectedArguments($method, 0, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectElements(): array {
		$paths = App::path('templates');

		$result = [];
		$result = $this->addElements($result, $paths);

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$paths = App::path('templates', $plugin);
			$result = $this->addElements($result, $paths, $plugin);
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
	protected function addElements(array $result, array $paths, ?string $plugin = null): array {
		foreach ($paths as $path) {
			$path .= 'element' . DS;
			if (!is_dir($path)) {
				continue;
			}

			$Directory = new RecursiveDirectoryIterator($path);
			$Iterator = new RecursiveIteratorIterator($Directory);
			$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

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
