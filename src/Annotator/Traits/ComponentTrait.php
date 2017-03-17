<?php
namespace IdeHelper\Annotator\Traits;

use Cake\Core\App;
use Cake\Core\Plugin;

/**
 * Handles component related things
 */
trait ComponentTrait {

	/**
	 * @param string $component
	 *
	 * @return string|null
	 */
	protected function _findClassName($component) {
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
