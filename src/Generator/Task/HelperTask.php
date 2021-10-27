<?php

namespace IdeHelper\Generator\Task;

use Cake\Filesystem\Folder;
use Cake\View\View;
use Cake\View\ViewBuilder;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;

class HelperTask implements TaskInterface {

	public const CLASS_VIEW = View::class;
	public const CLASS_VIEW_BUILDER = ViewBuilder::class;

	/**
	 * @var string
	 */
	protected const METHOD_VIEW = '\\' . self::CLASS_VIEW . '::loadHelper(0)';

	/**
	 * @var string
	 */
	protected const METHOD_VIEW_BUILDER = '\\' . self::CLASS_VIEW_BUILDER . '::addHelper()';

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$helpers = $this->collectHelpers();

		$map = [];
		foreach ($helpers as $name => $className) {
			$map[$name] = ClassName::create($className);
		}
		ksort($map);

		$result = [];

		$directive = new Override(static::METHOD_VIEW, $map);
		$result[$directive->key()] = $directive;

		$list = [];
		foreach ($helpers as $name => $className) {
			$list[$name] = "'$name'";
		}
		ksort($list);

		$directive = new ExpectedArguments(static::METHOD_VIEW_BUILDER, 0, $list);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectHelpers(): array {
		$helpers = [];

		$folders = array_merge(App::core('View/Helper'), AppPath::get('View/Helper'));
		foreach ($folders as $folder) {
			$helpers = $this->addHelpers($helpers, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('View/Helper', $plugin);
			foreach ($folders as $folder) {
				$helpers = $this->addHelpers($helpers, $folder, $plugin);
			}
		}

		return $helpers;
	}

	/**
	 * @param array<string> $helpers
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string>
	 */
	protected function addHelpers(array $helpers, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Helper\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'View/Helper', 'Helper');
			if (!$className) {
				continue;
			}

			$helpers[$name] = $className;
		}

		return $helpers;
	}

}
