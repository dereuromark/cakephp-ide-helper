<?php

namespace IdeHelper\Annotator\Traits;

use IdeHelper\Utility\App;
use IdeHelper\Utility\Plugin;

/**
 * Handles component related things
 */
trait HelperTrait {

	/**
	 * @param string $helper
	 * @param bool $includeApp
	 *
	 * @return string|null
	 */
	protected function findClassName(string $helper, bool $includeApp): ?string {
		$className = App::className($helper, 'View/Helper', 'Helper', $includeApp);
		if ($className) {
			return $className;
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$className = App::className($plugin . '.' . $helper, 'View/Helper', 'Helper');
			if ($className) {
				return $className;
			}
		}

		return null;
	}

}
