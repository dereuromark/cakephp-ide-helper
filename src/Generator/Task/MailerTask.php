<?php

namespace IdeHelper\Generator\Task;

use Cake\Filesystem\Folder;
use Cake\Mailer\MailerAwareTrait;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;

class MailerTask implements TaskInterface {

	public const CLASS_MAILER = MailerAwareTrait::class;

	/**
	 * @var string
	 */
	protected static $alias = '\\' . self::CLASS_MAILER . '::getMailer(0)';

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$map = [];

		$mailers = $this->collectMailers();
		foreach ($mailers as $name => $className) {
			$map[$name] = ClassName::create($className);
		}

		ksort($map);

		$result = [];
		if ($map) {
			$directive = new Override(static::$alias, $map);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectMailers(): array {
		$mailers = [];

		$folders = AppPath::get('Mailer');
		foreach ($folders as $folder) {
			$mailers = $this->addMailers($mailers, $folder);
		}

		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('Mailer', $plugin);
			foreach ($folders as $folder) {
				$mailers = $this->addMailers($mailers, $folder, $plugin);
			}
		}

		return $mailers;
	}

	/**
	 * @param array<string> $components
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return array<string>
	 */
	protected function addMailers(array $components, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Mailer\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'Mailer', 'Mailer');
			if (!$className) {
				continue;
			}

			$components[$name] = $className;
		}

		return $components;
	}

}
