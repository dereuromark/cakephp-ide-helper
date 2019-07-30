<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\Plugin;
use Cake\View\View;
use IdeHelper\Utility\AppPath;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class ElementTask extends ModelTask {

	const CLASS_VIEW = View::class;

	/**
	 * @return array
	 */
	public function collect() {
		$result = [];

		$elements = $this->collectElements();
		$map = [];
		foreach ($elements as $element) {
			$map[$element] = '\\' . static::CLASS_VIEW . '::class';
		}

		$result['\\' . static::CLASS_VIEW . '::element(0)'] = $map;

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectElements() {
		$paths = AppPath::get('Template');

		$result = [];
		$result = $this->addElements($result, $paths);

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$paths = AppPath::get('Template', $plugin);
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
	protected function addElements(array $result, array $paths, $plugin = null) {
		foreach ($paths as $path) {
			$path .= 'Element' . DS;
			if (!is_dir($path)) {
				continue;
			}

			$Directory = new RecursiveDirectoryIterator($path);
			$Iterator = new RecursiveIteratorIterator($Directory);
			$Regex = new RegexIterator($Iterator, '/^.+\.ctp$/i', RecursiveRegexIterator::GET_MATCH);

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
