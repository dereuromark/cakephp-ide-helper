<?php

namespace IdeHelper\Utility;

use App\Controller\AppController;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Throwable;

class ControllerActionParser {

	/**
	 * @var array<string>|null
	 */
	protected static $appControllerActions;

	/**
	 * @param string $path
	 *
	 * @return array<string>
	 */
	public function parse(string $path): array {
		$actions = $this->parseFile($path);

		if (static::$appControllerActions === null) {
			try {
				$class = new ReflectionClass(AppController::class);
				$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
			} catch (Throwable $exception) {
				return [];
			}

			static::$appControllerActions = [];
			foreach ($methods as $method) {
				static::$appControllerActions[] = $method->getName();
			}
		}

		$actions = array_diff($actions, static::$appControllerActions);

		return array_values($actions);
	}

	/**
	 * @param string $path
	 *
	 * @return array<string>
	 */
	protected function parseFile($path): array {
		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}

		preg_match_all('/public function (.+)\(/', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		return $matches[1];
	}

}
