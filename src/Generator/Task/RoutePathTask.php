<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\UrlHelper;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\StringName;

class RoutePathTask implements TaskInterface {

	public const CLASS_ROUTER = Router::class;
	public const CLASS_URL_HELPER = UrlHelper::class;
	public const CLASS_HTML_HELPER = HtmlHelper::class;
	public const SET_PATHS = 'paths';

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectPaths();
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_PATHS, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		$method = '\\' . static::CLASS_ROUTER . '::pathUrl()';
		$directive = new ExpectedArguments($method, 0, [$registerArgumentsSet]);
		$result[$directive->key()] = $directive;

		$method = '\\' . static::CLASS_URL_HELPER . '::buildFromPath()';
		$directive = new ExpectedArguments($method, 0, [$registerArgumentsSet]);
		$result[$directive->key()] = $directive;

		$method = '\\' . static::CLASS_HTML_HELPER . '::linkFromPath()';
		$directive = new ExpectedArguments($method, 1, [$registerArgumentsSet]);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectPaths(): array {
		$plugins = Plugin::all();

		$paths = AppPath::get('Controller');

		$controllers = [];
		foreach ($paths as $path) {
			$controllers += $this->_controllers($path);
		}

		foreach ($plugins as $plugin) {
			$path = Plugin::classPath($plugin) . 'Controller' . DS;
			$controllers += $this->_controllers($path, $plugin);
		}

		ksort($controllers);

		return $controllers;
	}

	/**
	 * @param string $folder
	 * @param string|null $plugin
	 * @param string|null $prefix
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

			$controllers[$routePath] = StringName::create($routePath);
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
