<?php
namespace IdeHelper\Generator\Task;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;

class RoutePathTask implements TaskInterface {

	const CLASS_ROUTER = Router::class;

	/**
	 * @return array
	 */
	public function collect(): array {
		$result = [];

		$map = $this->collectPaths();

		$result['\\' . static::CLASS_ROUTER . '::pathUrl(0)'] = $map;

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectPaths(): array {

		$plugins = Plugin::loaded();

		$paths = App::path('Controller');

		$controllers = [];
		foreach ($paths as $path) {
			$controllers += $this->_controllers($path);
		}

		foreach ($plugins as $plugin) {
			$path = Plugin::classPath($plugin) . 'Controller' . DS;
			$controllers += $this->_controllers($path, $plugin);
		}

		return $controllers;
	}

	/**
	 * @param string $folder
	 * @return string[]
	 */
	protected function _controllers(string $folder, ?string $plugin = null, ?string $prefix = null): array {
		$controllers = [];

		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			$name = basename($file);
			preg_match('/^(.+)Controller\.php$/', $name, $matches);
			if (!$matches || $matches[1] === 'App') {
				continue;
			}
			$controllerName = $matches[1];

			$routePath = $controllerName . '::action';
			if ($prefix) {
				$routePath = $prefix . '/' . $routePath;
			}
			if ($plugin) {
				$routePath = $plugin . '.' . $routePath;
			}

			$controllers[$routePath] = 'string';
		}

		foreach ($folderContent[0] as $subFolder) {
			$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

			if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
				continue;
			}

			$sub = $this->_controllers($folder . $subFolder . DS, $plugin, $prefix);
			$controllers += $sub;
		}

		return $controllers;
	}

}
