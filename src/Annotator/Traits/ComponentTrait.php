<?php

namespace IdeHelper\Annotator\Traits;

use IdeHelper\Utility\App;
use IdeHelper\Utility\Plugin;

/**
 * Handles component related things
 */
trait ComponentTrait {

	/**
	 * @param string $component
	 * @param bool $includeApp
	 *
	 * @return string|null
	 */
	protected function findClassName(string $component, bool $includeApp): ?string {
		$plugins = Plugin::all();
		if (class_exists($component)) {
			return $component;
		}

		$className = App::className($component, 'Controller/Component', 'Component', $includeApp);
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
