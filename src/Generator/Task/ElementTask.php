<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\View\View;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class ElementTask extends ModelTask {

	const CLASS_VIEW = View::class;

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$result = [];

		$elements = $this->collectElements();
		$map = [];
		foreach ($elements as $element) {
			$map[$element] = ClassName::create(static::CLASS_VIEW);
		}

		ksort($map);

		$method = '\\' . static::CLASS_VIEW . '::element(0)';
		$directive = new Override($method, $map);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return string[]
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
	 * @param string[] $result
	 * @param string[] $paths
	 * @param string|null $plugin
	 *
	 * @return string[]
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
