<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\View\View;
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
	 * @return array
	 */
	protected function collectElements() {
		$paths = App::path('Template');

		$result = [];
		$result = $this->addElements($result, $paths);

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$paths = App::path('Template', $plugin);
			$result = $this->addElements($result, $paths, $plugin);
		}

		return $result;
	}

	/**
	 * @param array $result
	 * @param array $paths
	 * @param string|null $plugin
	 *
	 * @return array
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
				if ($plugin) {
					$name = $plugin . '.' . $name;
				}
				$result[] = $name;
			}
		}

		return $result;
	}

}
