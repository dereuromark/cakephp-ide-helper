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
use IdeHelper\Utility\ControllerActionParser;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\StringName;

class RoutePathTask implements TaskInterface {

	public const CLASS_ROUTER = Router::class;
	public const CLASS_URL_HELPER = UrlHelper::class;
	public const CLASS_HTML_HELPER = HtmlHelper::class;

	/**
	 * @var string
	 */
	public const SET_ROUTE_PATHS = 'routePaths';

	/**
	 * @var \IdeHelper\Utility\ControllerActionParser
	 */
	protected $controllerActionParser;

	public function __construct() {
		$this->controllerActionParser = new ControllerActionParser();
	}

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectPaths();
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_ROUTE_PATHS, $list);
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

		$method = '\\urlArray()';
		$directive = new ExpectedArguments($method, 0, [$registerArgumentsSet]);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectPaths(): array {
		$plugins = Plugin::all();

		$controllerPaths = AppPath::get('Controller');

		$paths = [];
		foreach ($controllerPaths as $controllerPath) {
			$paths += $this->_paths($controllerPath);
		}

		foreach ($plugins as $plugin) {
			$pluginControllerPath = Plugin::classPath($plugin) . 'Controller' . DS;
			$paths += $this->_paths($pluginControllerPath, $plugin);
		}

		ksort($paths);

		return $paths;
	}

	/**
	 * @param string $folder
	 * @param string|null $plugin
	 * @param string|null $prefix
	 * @return array<string>
	 */
	protected function _paths(string $folder, ?string $plugin = null, ?string $prefix = null): array {
		$paths = [];

		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			$name = basename($file);
			preg_match('/^(.+)Controller\.php$/', $name, $matches);
			if (!$matches || $matches[1] === 'App') {
				continue;
			}
			$controllerName = $matches[1];

			$actions = $this->controllerActionParser->parse($folder . $file);
			foreach ($actions as $action) {
				$routePath = $controllerName . '::' . $action;
				if ($prefix) {
					$routePath = $prefix . '/' . $routePath;
				}
				if ($plugin) {
					$routePath = $plugin . '.' . $routePath;
				}

				$paths[$routePath] = StringName::create($routePath);
			}
		}

		foreach ($folderContent[0] as $subFolder) {
			$prefixes = (array)Configure::read('IdeHelper.prefixes') ?: null;

			if ($prefixes !== null && !in_array($subFolder, $prefixes, true)) {
				continue;
			}

			$sub = $this->_paths($folder . $subFolder . DS, $plugin, $subFolder);
			$paths += $sub;
		}

		return $paths;
	}

}
