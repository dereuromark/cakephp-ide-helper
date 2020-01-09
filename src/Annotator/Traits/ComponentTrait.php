<?php

namespace IdeHelper\Annotator\Traits;

use Cake\Core\Plugin;
use IdeHelper\Utility\App;

/**
 * Handles component related things
 */
trait ComponentTrait {

	/**
	 * @param string $component
	 *
	 * @return string|null
	 */
	protected function findClassName(string $component): ?string {
		$plugins = Plugin::loaded();
		if (class_exists($component)) {
			return $component;
		}

		$className = App::className($component, 'Controller/Component', 'Component');
		if ($className) {
			return $className;
		}

		foreach ($plugins as $plugin) {
			$className = App::className($plugin . '.' . $component, 'Controller/Component', 'Component');
			if ($className) {
				return $className;
			}
		}

		return null;
	}

}
