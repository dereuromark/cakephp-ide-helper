<?php

namespace IdeHelper\CodeCompletion\Task;

use Cake\Filesystem\Folder;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;

class BehaviorTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const TYPE_NAMESPACE = 'Cake\ORM';

	/**
	 * @return string
	 */
	public function type(): string {
		return static::TYPE_NAMESPACE;
	}

	/**
	 * @return string
	 */
	public function create(): string {
		$behaviors = $this->collectBehaviors();
		if (!$behaviors) {
			return '';
		}

		$content = $this->build($behaviors);

		$content = <<<CODE
abstract class BehaviorRegistry extends \Cake\Core\ObjectRegistry {

$content

}

CODE;

		return $content;
	}

	/**
	 * @return array<string>
	 */
	protected function collectBehaviors(): array {
		$behaviors = [];

		$folders = array_merge(App::core('ORM/Behavior'), AppPath::get('Model/Behavior'));
		foreach ($folders as $folder) {
			$behaviors = $this->addBehaviors($behaviors, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Model/Behavior', $plugin);
			foreach ($folders as $folder) {
				$behaviors = $this->addBehaviors($behaviors, $folder, $plugin);
			}
		}

		return $behaviors;
	}

	/**
	 * @param array<string> $behaviors
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string>
	 */
	protected function addBehaviors(array $behaviors, string $folder, ?string $plugin = null): array {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Behavior\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'Model/Behavior', 'Behavior');
			if (!$className) {
				continue;
			}

			$behaviors[$name] = $className;
		}

		return $behaviors;
	}

	/**
	 * @param array<string> $behaviors
	 *
	 * @return string
	 */
	protected function build(array $behaviors): string {
		$result = [];

		foreach ($behaviors as $behavior => $className) {
			[$plugin, $name] = pluginSplit($behavior);

			$template = <<<CODE
	/**
	 * $behavior behavior.
	 *
	 * @var \\$className
	 */
	public \$$name;
CODE;
			$result[] = $template;
		}

		return implode(PHP_EOL . PHP_EOL, $result);
	}

}
