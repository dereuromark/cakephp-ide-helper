<?php

namespace IdeHelper\Annotator;

use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use IdeHelper\Utility\App;
use RuntimeException;
use Throwable;

class ShellAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate(string $path): bool {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Shell' || substr($className, -5) !== 'Shell' && substr($className, -4) !== 'Task') {
			return false;
		}

		$content = file_get_contents($path);
		if ($content === false) {
			throw new RuntimeException('Cannot read file');
		}
		$primaryModelClass = $this->getPrimaryModelClass($content);
		$usedModels = $this->getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->getModelAnnotations($usedModels, $content);

		$usedTasks = $this->getUsedTasks($className);
		foreach ($usedTasks as $alias => $usedTask) {
			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $usedTask['fullClass'], '$' . $alias);
		}

		return $this->annotateContent($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function getPrimaryModelClass(string $content): ?string {
		if (!preg_match('/\bprotected \$modelClass = \'([a-z.\/]+)\'/i', $content, $matches)) {
			return null;
		}

		/** @var string $modelName */
		$modelName = $matches[1];

		return $modelName;
	}

	/**
	 * @param string $content
	 *
	 * @return array<string>
	 */
	protected function getUsedModels(string $content): array {
		preg_match_all('/\$this->loadModel\(\'([a-z.\/]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

	/**
	 * @param string $name
	 *
	 * @throws \Exception
	 *
	 * @return array<string, array<string, mixed>>
	 */
	protected function getUsedTasks(string $name): array {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		$fullName = ($plugin ? $plugin . '.' : '') . $name;
		if (substr($name, -4) === 'Task') {
			/** @phpstan-var class-string<object>|null $className */
			$className = App::className($fullName, 'Shell/Task');
		} else {
			/** @phpstan-var class-string<object>|null $className */
			$className = App::className($fullName, 'Shell');
		}
		if (!$className) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping shell task annotations: Invalid class name (or content) ' . $fullName);
			}

			return [];
		}

		if ($this->_isAbstract($className)) {
			return [];
		}

		try {
			/** @var \Cake\Console\Shell $object */
			$object = new $className();
			$object->loadTasks();
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping shell task annotations: ' . $e->getMessage());
			}

			return [];
		}

		$map = $this->invokeProperty($object, '_taskMap');
		if (!$map) {
			return [];
		}
		foreach ($map as $alias => $row) {
			$fullClass = App::className($row['class'], 'Shell/Task', 'Task');
			if (!$fullClass) {
				$this->_io->warn('   Skipping invalid task ' . $alias . ': ' . $row['class']);
				unset($map[$alias]);

				continue;
			}
			$map[$alias]['fullClass'] = $fullClass;
		}

		return $map;
	}

}
